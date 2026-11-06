<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WorkerScoringService;
use App\Services\PricingService;
use App\Services\HaversineService;

class BookingController extends Controller
{
    protected WorkerScoringService $scoringService;
    protected PricingService $pricingService;
    protected HaversineService $haversineService;

    public function __construct(
        WorkerScoringService $scoringService,
        PricingService $pricingService,
        HaversineService $haversineService
    ) {
        $this->scoringService = $scoringService;
        $this->pricingService = $pricingService;
        $this->haversineService = $haversineService;
    }

    /**
     * Tampilkan daftar booking untuk admin
     */
    public function index()
    {
        $totalBooking = DB::table('request')->count();
        $waitingBooking = DB::table('request')->where('status_admin', 'belum_diterima')->count();
        $approvalWaiting = DB::table('request')->where('status', 'persetujuan_dikirim')->count();
        $requests = \App\Models\Request::with(['user', 'worker', 'offers'])
            ->orderByRaw("CASE urgensi WHEN 'tinggi' THEN 3 WHEN 'sedang' THEN 2 ELSE 1 END DESC")
            ->orderByDesc('created_at')
            ->get();

        $workers = DB::table('workers')->where('status', 'aktif')->get();

        return view('admin.booking', compact('totalBooking', 'waitingBooking', 'approvalWaiting', 'requests', 'workers'));
    }

    /**
     * Tampilkan detail booking beserta panel Rekomendasi Tukang (Algoritma)
     */
    public function show($id)
    {
        $booking = DB::table('request')
            ->leftJoin('users', 'request.id_user', '=', 'users.id')
            ->leftJoin('workers', 'request.id_worker', '=', 'workers.id')
            ->select(
                'request.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.avatar as user_avatar',
                'workers.name as worker_name',
                'workers.phone as worker_phone'
            )
            ->where('request.id_request', $id)
            ->first();

        if (!$booking) {
            abort(404, 'Data booking tidak ditemukan.');
        }

        // Ambil hasil rekomendasi tukang dari tabel rekomendasi_tukang
        $rekomendasi = DB::table('rekomendasi_tukang')
            ->join('workers', 'rekomendasi_tukang.id_worker', '=', 'workers.id')
            ->where('rekomendasi_tukang.id_request', $id)
            ->select(
                'rekomendasi_tukang.*',
                'workers.name',
                'workers.phone',
                'workers.skills',
                'workers.rating',
                'workers.pengalaman',
                'workers.alamat',
                'workers.status as worker_status'
            )
            ->orderByDesc('rekomendasi_tukang.skor_rating')
            ->orderBy('workers.id')
            ->get();

        // Jika belum ada rekomendasi dan koordinat ada, hitung otomatis
        if ($rekomendasi->isEmpty() && !empty($booking->latitude) && !empty($booking->longitude)) {
            $skillNeedle = $booking->penawaran_keahlian ?: $booking->layanan;
            $ranked = $this->scoringService->rankWorkers($skillNeedle, (float)$booking->latitude, (float)$booking->longitude);
            if (!empty($ranked)) {
                $this->scoringService->saveRecommendations((int)$booking->id_request, $ranked);
                $rekomendasi = DB::table('rekomendasi_tukang')
                    ->join('workers', 'rekomendasi_tukang.id_worker', '=', 'workers.id')
                    ->where('rekomendasi_tukang.id_request', $id)
                    ->select(
                        'rekomendasi_tukang.*',
                        'workers.name',
                        'workers.phone',
                        'workers.skills',
                        'workers.rating',
                        'workers.pengalaman',
                        'workers.alamat',
                        'workers.status as worker_status'
                    )
                    ->orderByDesc('rekomendasi_tukang.skor_rating')
                    ->orderBy('workers.id')
                    ->get();
            }
        }

        $allWorkers = DB::table('workers')->where('status', 'aktif')->get();
        $activeWorkerCounts = DB::table('request')
            ->select('id_worker', DB::raw('COUNT(*) as active_count'))
            ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])
            ->whereNotNull('id_worker')
            ->groupBy('id_worker');
        $workerOffers = DB::table('worker_offers')
            ->join('workers', 'worker_offers.id_worker', '=', 'workers.id')
            ->leftJoinSub($activeWorkerCounts, 'active_jobs', function ($join) {
                $join->on('workers.id', '=', 'active_jobs.id_worker');
            })
            ->where('worker_offers.id_request', $id)
            ->orderBy('worker_offers.total_estimasi')
            ->select('worker_offers.*', 'workers.name', 'workers.foto', 'workers.phone', 'workers.status as worker_status', 'workers.status_ketersediaan', 'active_jobs.active_count')
            ->get();
        foreach ($workerOffers as $offer) {
            $offer->availability_label = $offer->worker_status !== 'aktif'
                || in_array($offer->status_ketersediaan, ['tidak_tersedia', 'libur'], true)
                ? 'Tidak Tersedia'
                : (((int) $offer->active_count > 0 || $offer->status_ketersediaan === 'sedang_bekerja') ? 'Sedang Bekerja' : 'Siap Bekerja');
        }
        $selectedOffer = $booking->id_penawaran_terpilih
            ? $workerOffers->firstWhere('id', $booking->id_penawaran_terpilih)
            : null;

        return view('admin.booking.show', compact('booking', 'rekomendasi', 'allWorkers', 'workerOffers', 'selectedOffer'));
    }

    /**
     * Hitung Ulang Rekomendasi Tukang berdasarkan koordinat request
     */
    public function hitungUlang($id)
    {
        $booking = DB::table('request')->where('id_request', $id)->first();
        if (!$booking) {
            return back()->with('error', 'Booking tidak ditemukan.');
        }

        if (empty($booking->latitude) || empty($booking->longitude)) {
            return back()->with('error', 'Koordinat lokasi user tidak tersedia pada booking ini.');
        }

        $skillNeedle = $booking->penawaran_keahlian ?: $booking->layanan;
        $ranked = $this->scoringService->rankWorkers($skillNeedle, (float)$booking->latitude, (float)$booking->longitude);

        if (empty($ranked)) {
            return back()->with('warning', 'Tidak ditemukan tukang yang memenuhi kriteria dalam radius 15 km.');
        }

        $this->scoringService->saveRecommendations((int)$booking->id_request, $ranked);

        return back()->with('success', 'Rekomendasi tukang berhasil dihitung ulang!');
    }

    public function buatPenawaran(Request $request, $id)
    {
        $validated = $request->validate([
            'tingkat_kerusakan' => 'required|in:ringan,sedang,berat',
            'kompleksitas' => 'required|in:rendah,sedang,tinggi',
            'urgensi' => 'required|in:rendah,sedang,tinggi',
            'penawaran_keahlian' => 'required|string|max:150',
            'biaya_material' => 'required|numeric|min:0|max:9999999999',
            'penawaran_catatan' => 'nullable|string|max:2000',
        ]);

        $booking = DB::table('request')->where('id_request', $id)->first();
        if (!$booking) {
            return back()->with('error', 'Request tidak ditemukan.');
        }
        if (!in_array($booking->status, ['menunggu', 'menunggu_persetujuan'], true)
            || $booking->status_admin !== 'belum_diterima'
            || $booking->persetujuan_user_at !== null) {
            return back()->with('error', 'Penawaran tidak dapat diubah setelah user menyetujuinya atau request selesai.');
        }

        $service = DB::table('services')->where('nama', $booking->layanan)->first();
        if (!$service) {
            return back()->with('error', 'Data tarif layanan tidak ditemukan.');
        }
        if ($booking->latitude === null || $booking->longitude === null) {
            return back()->with('error', 'Koordinat request tidak tersedia untuk menghitung biaya jarak.');
        }

        $rankedWorkers = $this->scoringService->rankWorkers(
            $validated['penawaran_keahlian'],
            (float) $booking->latitude,
            (float) $booking->longitude
        );
        if (empty($rankedWorkers)) {
            return back()->with('error', 'Tidak ada tukang siap bekerja yang cocok dengan keahlian ini.');
        }
        if ($booking->id_worker_pilihan) {
            usort($rankedWorkers, function ($left, $right) use ($booking) {
                return ((int) $right['id_worker'] === (int) $booking->id_worker_pilihan)
                    <=> ((int) $left['id_worker'] === (int) $booking->id_worker_pilihan);
            });
        }

        $office = $this->scoringService->getKantorCoordinates();
        $route = $this->haversineService->getDrivingRoute(
            $office['latitude'],
            $office['longitude'],
            (float) $booking->latitude,
            (float) $booking->longitude
        );
        if ($route === null) {
            return back()->with('error', 'Rute jalan tidak dapat dihitung. Penawaran belum dikirim. Coba kembali beberapa saat lagi.');
        }

        $biayaMaterial = (float) $validated['biaya_material'];
        $offers = [];
        foreach (array_slice($rankedWorkers, 0, 3) as $candidate) {
            $tarifDasar = (float) $candidate['tarif'] > 0
                ? (float) $candidate['tarif']
                : (float) $service->harga_min;
            $workerRate = $this->pricingService->adjustWorkerRate(
                $tarifDasar,
                (float) $candidate['rating'],
                (int) $candidate['pengalaman']
            );
            $pricing = $this->pricingService->calculate(
                hargaMin: $workerRate['harga_layanan'],
                hargaMax: $workerRate['harga_layanan'],
                durasiDasarMenit: (int) ($service->durasi_dasar_menit ?? 60),
                kompleksitas: $validated['kompleksitas'],
                urgensi: $validated['urgensi'],
                routeDistanceKm: $route['distance_km'],
                routeDurationMinutes: $route['duration_minutes'],
                tingkatKerusakan: $validated['tingkat_kerusakan'],
                biayaAdditional: $workerRate['biaya_additional'],
                tarifDasarPerhitungan: $workerRate['harga_dasar']
            );
            $faktorHarga = 1 + ($pricing['faktor_urgensi'] - 1) + ($pricing['faktor_kerusakan'] - 1);
            $hargaDasar = (float) $pricing['harga_layanan'];
            $biayaUrgensi = $pricing['biaya_urgensi'];
            $biayaKerusakan = $pricing['biaya_kerusakan'];
            $biayaJasa = round($hargaDasar + $biayaUrgensi + $biayaKerusakan);
            $jasaMaterial = round($biayaJasa + $biayaMaterial);
            $biayaTransport = (float) $pricing['biaya_transportasi'];
            $offers[] = [
                'id_worker' => $candidate['id_worker'],
                'biaya_jasa_material' => $jasaMaterial,
                'biaya_jasa_snapshot' => $biayaJasa,
                'biaya_material_snapshot' => $biayaMaterial,
                'harga_dasar_snapshot' => $workerRate['harga_dasar'],
                'biaya_kualifikasi_snapshot' => $workerRate['biaya_kualifikasi'],
                'biaya_rating_snapshot' => $workerRate['biaya_rating'],
                'biaya_pengalaman_snapshot' => $workerRate['biaya_pengalaman'],
                'pengalaman_persen_snapshot' => $workerRate['pengalaman_persen'],
                'biaya_urgensi_snapshot' => $biayaUrgensi,
                'biaya_kerusakan_snapshot' => $biayaKerusakan,
                'biaya_transportasi' => $biayaTransport,
                'total_estimasi' => round($jasaMaterial + $biayaTransport + $pricing['biaya_additional']),
                'jarak_km' => $route['distance_km'],
                'estimasi_durasi_menit' => $pricing['estimasi_durasi_menit'],
                'rating_snapshot' => $candidate['rating'],
                'biaya_additional_snapshot' => $pricing['biaya_additional'],
                'pengalaman_snapshot' => $candidate['pengalaman'],
                'jumlah_ulasan_snapshot' => $candidate['jumlah_ulasan'],
                'keahlian_snapshot' => $candidate['skills'] ?: $candidate['name'],
                'faktor_harga_snapshot' => $faktorHarga,
                'status' => 'ditawarkan',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $updated = DB::transaction(function () use ($id, $validated, $route, $biayaMaterial, $offers) {
            $locked = DB::table('request')->where('id_request', $id)->lockForUpdate()->first();
            if (!$locked
                || !in_array($locked->status, ['menunggu', 'menunggu_persetujuan'], true)
                || $locked->status_admin !== 'belum_diterima'
                || $locked->persetujuan_user_at !== null) {
                return false;
            }

            DB::table('request')->where('id_request', $id)->update([
                'tingkat_kerusakan' => $validated['tingkat_kerusakan'],
                'kompleksitas' => $validated['kompleksitas'],
                'urgensi' => $validated['urgensi'],
                'penawaran_keahlian' => $validated['penawaran_keahlian'],
                'biaya_material' => $biayaMaterial,
                'jarak_km' => $route['distance_km'],
                'durasi_perjalanan_menit' => $route['duration_minutes'],
                'harga_layanan' => null,
                'faktor_urgensi' => null,
                'biaya_jarak' => null,
                'estimasi_durasi_menit' => null,
                'estimasi_total' => null,
                'jumlah_pembayaran' => null,
                'id_penawaran_terpilih' => null,
                'penawaran_catatan' => $validated['penawaran_catatan'] ?? null,
                'penawaran_dikirim_at' => now(),
                'status' => 'menunggu_persetujuan',
                'updated_at' => now(),
            ]);

            DB::table('worker_offers')->where('id_request', $id)->delete();
            DB::table('rekomendasi_tukang')->where('id_request', $id)->delete();
            foreach ($offers as $offer) {
                DB::table('worker_offers')->insert(array_merge($offer, [
                    'id_request' => $id,
                ]));
            }

            return true;
        });

        if (!$updated) {
            return back()->with('error', 'Status request berubah. Muat ulang sebelum membuat penawaran.');
        }

        $this->scoringService->saveRecommendations((int) $id, $rankedWorkers);

        return redirect()->route('admin.booking.show', $id)->with('success', 'Penawaran harga berhasil dikirim kepada user.');
    }

    public function tugaskan(Request $request, $id)
    {
        $assigned = DB::transaction(function () use ($id) {
            $booking = DB::table('request')->where('id_request', $id)->lockForUpdate()->first();
            if (!$booking || $booking->status !== 'persetujuan_dikirim'
                || $booking->persetujuan_user_at === null || !$booking->id_penawaran_terpilih) {
                return false;
            }

            $offer = DB::table('worker_offers')
                ->where('id', $booking->id_penawaran_terpilih)
                ->where('id_request', $id)
                ->where('status', 'dipilih')
                ->lockForUpdate()
                ->first();
            if (!$offer) {
                return false;
            }

            $worker = DB::table('workers')->where('id', $offer->id_worker)->lockForUpdate()->first();
            $activeJob = DB::table('request')->where('id_worker', $offer->id_worker)
                ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])->exists();
            if (!$worker || $worker->status !== 'aktif'
                || $worker->status_ketersediaan !== 'siap'
                || $activeJob) {
                return false;
            }

            DB::table('request')->where('id_request', $id)->update([
                'id_worker' => $offer->id_worker,
                'status' => 'dikonfirmasi',
                'status_admin' => 'diterima',
                'harga_layanan' => $offer->biaya_jasa_snapshot,
                'harga_dasar_estimasi' => $offer->harga_dasar_snapshot,
                'biaya_kualifikasi_estimasi' => $offer->biaya_kualifikasi_snapshot,
                'biaya_rating_estimasi' => $offer->biaya_rating_snapshot,
                'biaya_pengalaman_estimasi' => $offer->biaya_pengalaman_snapshot,
                'pengalaman_persen_estimasi' => $offer->pengalaman_persen_snapshot,
                'biaya_additional_estimasi' => $offer->biaya_additional_snapshot ?? 0,
                'rating_estimasi' => $offer->rating_snapshot,
                'pengalaman_estimasi' => $offer->pengalaman_snapshot,
                'faktor_urgensi' => 1,
                'biaya_material' => $offer->biaya_material_snapshot,
                'biaya_jarak' => $offer->biaya_transportasi,
                'jarak_km' => $offer->jarak_km,
                'estimasi_durasi_menit' => $offer->estimasi_durasi_menit,
                'estimasi_total' => $offer->total_estimasi,
                'jumlah_pembayaran' => $offer->total_estimasi,
                'verifikasi_admin_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('worker_offers')->where('id', $offer->id)->update([
                'status' => 'ditugaskan',
                'ditugaskan_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('workers')->where('id', $offer->id_worker)->update([
                'status_ketersediaan' => 'sedang_bekerja',
                'updated_at' => now(),
            ]);

            return true;
        });

        if (!$assigned) {
            return back()->with('error', 'Penawaran terpilih tidak tersedia lagi. Periksa ketersediaan worker sebelum verifikasi.');
        }

        return redirect()->route('admin.booking.show', $id)
            ->with('success', 'Persetujuan diverifikasi dan tukang ditugaskan. Pemesan harus menyelesaikan pembayaran sebelum pengerjaan dimulai.');
    }

    public function ubahStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:dikerjakan,selesai',
        ]);

        $changed = DB::transaction(function () use ($id, $validated) {
            $booking = DB::table('request')->where('id_request', $id)->lockForUpdate()->first();
            if (!$booking || $booking->status_admin !== 'diterima' || $booking->id_worker === null) {
                return 'invalid_booking';
            }

            $allowedTransition = ($booking->status === 'dikonfirmasi' && $validated['status'] === 'dikerjakan')
                || ($booking->status === 'dikerjakan' && $validated['status'] === 'selesai');
            if (!$allowedTransition) {
                return 'invalid_transition';
            }
            if ($validated['status'] === 'dikerjakan' && (int) $booking->bayar_sekarang !== 1) {
                return 'payment_required';
            }

            DB::table('request')->where('id_request', $id)->update([
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

            if ($validated['status'] === 'selesai') {
                $hasOtherActiveJob = DB::table('request')
                    ->where('id_worker', $booking->id_worker)
                    ->where('id_request', '<>', $id)
                    ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])
                    ->exists();
                $worker = DB::table('workers')->where('id', $booking->id_worker)->lockForUpdate()->first();
                if (!$hasOtherActiveJob && $worker && $worker->status_ketersediaan === 'sedang_bekerja') {
                    DB::table('workers')->where('id', $booking->id_worker)->update([
                        'status_ketersediaan' => 'siap',
                        'updated_at' => now(),
                    ]);
                }
            }

            return 'changed';
        });

        if ($changed === 'payment_required') {
            return back()->with('error', 'Pekerjaan belum dapat dimulai. Pemesan harus menyelesaikan pembayaran terlebih dahulu.');
        }
        if ($changed !== 'changed') {
            return back()->with('error', 'Perubahan status tidak sesuai tahap pengerjaan atau tukang belum ditugaskan.');
        }

        $message = $validated['status'] === 'dikerjakan'
            ? 'Status pesanan diperbarui: pengerjaan dimulai.'
            : 'Pesanan ditandai selesai.';

        return redirect()->route('admin.booking.show', $id)->with('success', $message);
    }

    /**
     * Tolak Booking
     */
    public function tolak($id)
    {
        $rejected = DB::transaction(function () use ($id) {
            $booking = DB::table('request')->where('id_request', $id)->lockForUpdate()->first();
            if (!$booking || !in_array($booking->status, ['menunggu', 'menunggu_persetujuan'], true)
                || $booking->status_admin !== 'belum_diterima') {
                return false;
            }

            DB::table('request')->where('id_request', $id)->update([
                'status' => 'ditolak',
                'status_admin' => 'ditolak',
                'updated_at' => now(),
            ]);

            return true;
        });

        if (!$rejected) {
            return back()->with('error', 'Request tidak dapat ditolak pada status saat ini.');
        }

        return redirect()->back()->with('success', 'Booking berhasil ditolak.');
    }
}
