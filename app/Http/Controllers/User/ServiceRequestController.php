<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\HaversineService;
use App\Services\OpenStreetMapGeocodingService;
use App\Services\PricingService;

class ServiceRequestController extends Controller
{
    protected HaversineService $haversineService;

    public function __construct(HaversineService $haversineService)
    {
        $this->haversineService = $haversineService;
    }

    /**
     * Tampilkan halaman Form Request
     */
    public function index(Request $request, PricingService $pricingService)
    {
        $services = DB::table('services')->where('status', 'aktif')->get();
        $reviewStats = DB::table('worker_reviews')
            ->select('id_worker', DB::raw('COUNT(*) as jumlah_ulasan'), DB::raw('AVG(rating) as rating_ulasan'))
            ->groupBy('id_worker');
        $workers = DB::table('workers')
            ->leftJoinSub($reviewStats, 'review_stats', function ($join) {
                $join->on('workers.id', '=', 'review_stats.id_worker');
            })
            ->select('workers.*', DB::raw('COALESCE(review_stats.jumlah_ulasan, 0) as jumlah_ulasan'), 'review_stats.rating_ulasan')
            ->orderBy('name')
            ->get();

        foreach ($workers as $worker) {
            $hasActiveJob = DB::table('request')
                ->where('id_worker', $worker->id)
                ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])
                ->exists();
            $worker->display_availability = $worker->status !== 'aktif'
                || in_array($worker->status_ketersediaan, ['tidak_tersedia', 'libur'], true)
                ? 'Tidak Tersedia'
                : (($hasActiveJob || $worker->status_ketersediaan === 'sedang_bekerja') ? 'Sedang Bekerja' : 'Siap Bekerja');
            $worker->display_rating = $worker->jumlah_ulasan > 0
                ? (float) $worker->rating_ulasan
                : (float) ($worker->rating ?? 5);
        }

        $selectedLayanan = trim((string) $request->query('layanan', ''));

        if ($request->has('layanan')) {
            $selected = $services->first(function ($service) use ($selectedLayanan) {
                return strcasecmp(trim((string) $service->nama), $selectedLayanan) === 0;
            });

            if (!$selected) {
                return redirect()->route('dashboard_user')->with('error', 'Layanan yang Anda pilih tidak tersedia saat ini. Silakan pilih layanan lain.');
            }
        } else {
            $selected = $services->first();
        }

        $user = Auth::user();
        $pricingRules = $pricingService->getRules();

        return view('user.request', compact('services', 'selected', 'user', 'workers', 'pricingRules'));
    }

    public function reverseAddress(Request $request, OpenStreetMapGeocodingService $geocoder)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $address = $geocoder->reverse((float) $validated['latitude'], (float) $validated['longitude']);
        if ($address === null) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat otomatis tidak tersedia. Isi rincian alamat secara manual.',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'address' => $address,
        ]);
    }

    public function locateAddress(Request $request, OpenStreetMapGeocodingService $geocoder)
    {
        $validated = $request->validate([
            'alamat' => 'required|string|max:255',
        ]);

        $alamat = trim(preg_replace('/\s+/', ' ', $validated['alamat']) ?? '');
        if ($alamat === '') {
            return response()->json([
                'success' => false,
                'message' => 'Lengkapi alamat sebelum mencari titik peta.',
            ], 422);
        }

        $addressWithoutRtRw = preg_replace('/\b(?:RT|RW)\s*[:.]?\s*\d+[A-Za-z]?\b/iu', '', $alamat) ?? $alamat;
        $addressWithoutRtRw = preg_replace('/\s*\/\s*/', ' ', $addressWithoutRtRw) ?? $addressWithoutRtRw;
        $addressWithoutRtRw = preg_replace('/\s*,\s*/', ', ', $addressWithoutRtRw) ?? $addressWithoutRtRw;
        $addressWithoutRtRw = trim(preg_replace('/(?:,\s*){2,}/', ', ', $addressWithoutRtRw) ?? $addressWithoutRtRw, " ,\t\n\r\0\x0B");
        $candidateQueries = array_unique(array_filter([$alamat, $addressWithoutRtRw]));

        $location = null;
        foreach ($candidateQueries as $candidate) {
            $query = preg_match('/\bIndonesia\b/i', $candidate) ? $candidate : "{$candidate}, Indonesia";
            $location = $geocoder->forward($query);
            if ($location !== null) {
                break;
            }
        }

        if ($location === null) {
            return response()->json([
                'success' => false,
                'message' => 'Titik alamat belum ditemukan. Coba lengkapi nama jalan atau geser pin peta secara manual.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'location' => $location,
        ]);
    }

    /**
     * Endpoint AJAX untuk menghitung estimasi harga & durasi
     */
    public function hitung(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        $userLat = (float) $validated['latitude'];
        $userLon = (float) $validated['longitude'];
        $office = [
            'latitude' => HaversineService::OFFICE_LATITUDE,
            'longitude' => HaversineService::OFFICE_LONGITUDE,
            'name' => 'Kantor Pusat TEKSIKA - SMKN 1 Gunung Putri',
        ];
        $route = $this->haversineService->getDrivingRoute(
            $office['latitude'],
            $office['longitude'],
            $userLat,
            $userLon
        );

        if ($route === null) {
            return response()->json([
                'success' => false,
                'message' => 'Rute jalan tidak dapat dihitung saat ini. Silakan coba kembali beberapa saat lagi.',
                'route_unavailable' => true,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'route' => [
                'distance_km' => $route['distance_km'],
                'duration_minutes' => $route['duration_minutes'],
                'geometry' => $route['geometry'],
                'encoded_polyline' => $route['encoded_polyline'] ?? null,
                'source' => $route['source'] ?? 'osrm',
                'office' => $office,
            ],
        ]);
    }

    /**
     * Simpan Permintaan Layanan (Request)
     */
    public function store(Request $request, PricingService $pricingService)
    {
        $user = Auth::user();

        // 1. Terima dan Validasi Input termasuk Koordinat Rumah User
        $validated = $request->validate([
            'layanan' => 'required|string|max:100',
            'id_worker_pilihan' => 'nullable|integer|exists:workers,id',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'urgensi' => 'required|in:rendah,sedang,tinggi',
            'tingkat_kerusakan' => 'required|in:ringan,sedang,berat',
            'detail' => 'required|string',
            'metode_pembayaran' => 'required|in:E-wallet,Bank,COD',
            'foto' => 'required|array|min:1|max:5',
            'foto.*' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $service = DB::table('services')->where('nama', $validated['layanan'])->where('status', 'aktif')->first();
        if (!$service) {
            return back()->withInput()->with('error', 'Layanan tidak valid.');
        }

        $estimateWorker = null;
        $estimateWorkerRating = 0.0;
        if (!empty($validated['id_worker_pilihan'])) {
            $worker = DB::table('workers')
                ->leftJoinSub(
                    DB::table('worker_reviews')
                        ->select('id_worker', DB::raw('COUNT(*) as review_count'), DB::raw('AVG(rating) as review_average'))
                        ->groupBy('id_worker'),
                    'review_stats',
                    fn ($join) => $join->on('workers.id', '=', 'review_stats.id_worker')
                )
                ->where('workers.id', $validated['id_worker_pilihan'])
                ->select('workers.*', 'review_stats.review_count', 'review_stats.review_average')
                ->first();
            if (!$worker) {
                return back()->withInput()->with('error', 'Tukang pilihan tidak ditemukan. Silakan pilih tukang lain.');
            }
            $workerSkills = array_map('strtolower', array_map('trim', explode(',', $worker->skills ?? '')));
            $hasServiceSkill = in_array(strtolower($service->nama), $workerSkills, true);
            $hasActiveJob = DB::table('request')
                ->where('id_worker', $worker->id ?? 0)
                ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])
                ->exists();

            if (!$worker || $worker->status !== 'aktif' || ($worker->status_ketersediaan ?? 'siap') !== 'siap' || $hasActiveJob || !$hasServiceSkill) {
                return back()->withInput()->with('error', 'Tukang pilihan tidak tersedia atau tidak sesuai dengan layanan. Silakan pilih tukang lain.');
            }
            $estimateWorker = $worker;
            $estimateWorkerRating = $worker->review_count > 0
                ? (float) $worker->review_average
                : (float) ($worker->rating ?? 5);
        } else {
            $reviewStats = DB::table('worker_reviews')
                ->select('id_worker', DB::raw('COUNT(*) as review_count'), DB::raw('AVG(rating) as review_average'))
                ->groupBy('id_worker');
            $availableWorkers = DB::table('workers')
                ->leftJoinSub($reviewStats, 'review_stats', function ($join) {
                    $join->on('workers.id', '=', 'review_stats.id_worker');
                })
                ->where('status', 'aktif')
                ->where(function ($query) {
                    $query->where('workers.status_ketersediaan', 'siap')
                        ->orWhereNull('workers.status_ketersediaan');
                })
                ->select('workers.*', 'review_stats.review_count', 'review_stats.review_average')
                ->get();

            $workerCandidates = [];
            foreach ($availableWorkers as $worker) {
                $workerSkills = array_map('strtolower', array_map('trim', explode(',', $worker->skills ?? '')));
                if (!in_array(strtolower($service->nama), $workerSkills, true)) {
                    continue;
                }

                $hasActiveJob = DB::table('request')
                    ->where('id_worker', $worker->id)
                    ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])
                    ->exists();
                if (!$hasActiveJob) {
                    $workerCandidates[] = $worker;
                }
            }
            usort($workerCandidates, function ($left, $right) use ($service, $pricingService) {
                $leftBaseRate = (float) ($left->tarif ?? 0) > 0 ? (float) $left->tarif : (float) $service->harga_min;
                $rightBaseRate = (float) ($right->tarif ?? 0) > 0 ? (float) $right->tarif : (float) $service->harga_min;
                $leftRating = $left->review_count > 0 ? (float) $left->review_average : (float) ($left->rating ?? 5);
                $rightRating = $right->review_count > 0 ? (float) $right->review_average : (float) ($right->rating ?? 5);
                $leftRate = $pricingService->adjustWorkerRate($leftBaseRate, $leftRating, (int) ($left->pengalaman ?? 0), (int) ($left->poin ?? 0));
                $rightRate = $pricingService->adjustWorkerRate($rightBaseRate, $rightRating, (int) ($right->pengalaman ?? 0), (int) ($right->poin ?? 0));
                $leftPrice = $leftRate['harga_layanan'] + $leftRate['biaya_additional'];
                $rightPrice = $rightRate['harga_layanan'] + $rightRate['biaya_additional'];

                return $leftPrice <=> $rightPrice;
            });
            $estimateWorker = $workerCandidates[0] ?? null;
            $estimateWorkerRating = $estimateWorker
                ? ($estimateWorker->review_count > 0 ? (float) $estimateWorker->review_average : (float) ($estimateWorker->rating ?? 5))
                : 0.0;
        }

        $userLat = (float) $validated['latitude'];
        $userLon = (float) $validated['longitude'];
        $hargaDasar = $estimateWorker && (float) ($estimateWorker->tarif ?? 0) > 0
            ? (float) $estimateWorker->tarif
            : (float) $service->harga_min;
        $workerRate = $pricingService->adjustWorkerRate(
            $hargaDasar,
            $estimateWorker ? $estimateWorkerRating : 3.0,
            (int) ($estimateWorker->pengalaman ?? 0),
            (int) ($estimateWorker->poin ?? 0)
        );
        $hargaAwal = $workerRate['harga_layanan'];
        $pricing = $pricingService->calculate(
            hargaMin: $hargaAwal,
            hargaMax: $hargaAwal,
            durasiDasarMenit: (int) ($service->durasi_dasar_menit ?? 60),
            kompleksitas: 'sedang',
            urgensi: $validated['urgensi'],
            userLat: $userLat,
            userLon: $userLon,
            tingkatKerusakan: $validated['tingkat_kerusakan'],
            biayaAdditional: $workerRate['biaya_additional'],
            tarifDasarPerhitungan: $workerRate['harga_dasar']
        );
        $biayaUrgensi = $pricing['biaya_urgensi'];
        $biayaKerusakan = $pricing['biaya_kerusakan'];

        // Upload foto
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $fotoPaths[] = $foto->store('request_foto', 'public');
            }
        }

        $userId = Auth::id() ?? 1;

        // 4. Simpan Koordinat Rumah User ke Tabel `request`
        $idRequest = DB::table('request')->insertGetId([
            'id_user' => $userId,
            'id_worker_pilihan' => $validated['id_worker_pilihan'] ?? null,
            'layanan' => $service->nama,
            'lokasi' => $validated['lokasi'],
            'latitude' => $userLat,
            'longitude' => $userLon,
            'urgensi' => $validated['urgensi'],
            'tingkat_kerusakan' => $validated['tingkat_kerusakan'],
            'kompleksitas' => 'sedang',
            'id_worker_estimasi' => $estimateWorker->id ?? null,
            'jarak_km' => $pricing['jarak_km'],
            'harga_dasar_estimasi' => $workerRate['harga_dasar'],
            'biaya_kualifikasi_estimasi' => $workerRate['biaya_kualifikasi'],
            'biaya_rating_estimasi' => $workerRate['biaya_rating'],
            'biaya_pengalaman_estimasi' => $workerRate['biaya_pengalaman'],
            'pengalaman_persen_estimasi' => $workerRate['pengalaman_persen'],
            'poin_estimasi' => $workerRate['poin'],
            'poin_persen_estimasi' => $workerRate['poin_persen'],
            'biaya_additional_estimasi' => $pricing['biaya_additional'],
            'rating_estimasi' => $estimateWorker ? $estimateWorkerRating : null,
            'pengalaman_estimasi' => $estimateWorker ? (int) ($estimateWorker->pengalaman ?? 0) : null,
            'harga_layanan' => $hargaAwal,
            'faktor_urgensi' => $pricing['faktor_urgensi'],
            'biaya_urgensi_estimasi' => $biayaUrgensi,
            'biaya_kerusakan_estimasi' => $biayaKerusakan,
            'biaya_jarak' => $pricing['biaya_jarak'],
            'estimasi_total' => $pricing['estimasi_total'],
            'estimasi_durasi_menit' => $pricing['estimasi_durasi_menit'],
            'durasi_perjalanan_menit' => $pricing['durasi_tempuh_menit'],
            'detail' => $validated['detail'],
            'status' => 'menunggu',
            'status_admin' => 'belum_diterima',
            'tanggal' => now(),
            'foto' => implode(',', $fotoPaths),
            'jumlah_pembayaran' => null,
            'bayar_sekarang' => 0,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Generate kode booking
        $kodeBooking = 'BK-' . str_pad($idRequest, 3, '0', STR_PAD_LEFT);
        DB::table('request')->where('id_request', $idRequest)->update(['kode_booking' => $kodeBooking]);

        // 5. Simpan / Perbarui Koordinat Rumah User ke Profil Pengguna (Tabel `users`)
        if (Auth::check()) {
            DB::table('users')->where('id', $userId)->update([
                'alamat' => $validated['lokasi'],
                'latitude' => $userLat,
                'longitude' => $userLon,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('bookinguser')->with('success', 'Permintaan layanan berhasil dikirim. Admin akan memeriksa kerusakan dan menyiapkan penawaran tukang.');
    }
}
