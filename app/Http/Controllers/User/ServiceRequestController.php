<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\PricingService;
use App\Services\WorkerScoringService;

class ServiceRequestController extends Controller
{
    protected PricingService $pricingService;
    protected WorkerScoringService $workerScoringService;

    public function __construct(PricingService $pricingService, WorkerScoringService $workerScoringService)
    {
        $this->pricingService = $pricingService;
        $this->workerScoringService = $workerScoringService;
    }

    /**
     * Tampilkan halaman Form Request
     */
    public function index(Request $request)
    {
        $services = DB::table('services')->where('status', 'aktif')->get();
        $selectedLayanan = $request->query('layanan');
        $selected = $services->firstWhere('nama', $selectedLayanan);

        return view('user.request', compact('services', 'selected'));
    }

    /**
     * Endpoint AJAX untuk menghitung estimasi harga & durasi
     */
    public function hitung(Request $request)
    {
        $request->validate([
            'layanan' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'kompleksitas' => 'nullable|string|in:rendah,sedang,tinggi',
            'urgensi' => 'nullable|string',
        ]);

        $serviceName = $request->input('layanan');
        $userLat = (float) $request->input('latitude');
        $userLon = (float) $request->input('longitude');
        $kompleksitas = $request->input('kompleksitas', 'sedang');
        $urgensi = $request->input('urgensi', 'rendah');

        // Cari master service
        $service = DB::table('services')
            ->where('nama', $serviceName)
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan yang dipilih tidak ditemukan.',
            ], 404);
        }

        // Jalankan ranking tukang dengan Haversine + Scoring
        $rankedWorkers = $this->workerScoringService->rankWorkers($service->nama, $userLat, $userLon);

        if (empty($rankedWorkers)) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada tukang yang tersedia dalam radius 15 km dari lokasi Anda.',
            ], 200);
        }

        // Ambil jarak tukang terdekat untuk perhitungan harga
        $closestDistance = $rankedWorkers[0]['jarak_km'];
        $durasiDasar = isset($service->durasi_dasar_menit) ? (int) $service->durasi_dasar_menit : 60;

        $estimasi = $this->pricingService->calculate(
            (float) $service->harga_min,
            (float) $service->harga_max,
            $durasiDasar,
            $kompleksitas,
            $urgensi,
            $closestDistance
        );

        return response()->json([
            'success' => true,
            'estimasi' => $estimasi,
            'tukang_terdekat' => [
                'name' => $rankedWorkers[0]['name'],
                'jarak_km' => $rankedWorkers[0]['jarak_km'],
                'rating' => $rankedWorkers[0]['rating'],
                'pengalaman' => $rankedWorkers[0]['pengalaman'],
            ],
            'total_kandidat' => count($rankedWorkers),
        ]);
    }

    /**
     * Simpan Permintaan Layanan (Request)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'layanan' => 'required|string|max:100',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'kompleksitas' => 'required|in:rendah,sedang,tinggi',
            'urgensi' => 'required|string',
            'detail' => 'required|string',
            'metode_pembayaran' => 'required|in:E-wallet,Bank,COD',
            'foto.*' => 'nullable|image|max:5120',
        ]);

        $service = DB::table('services')->where('nama', $validated['layanan'])->first();
        if (!$service) {
            return back()->withInput()->with('error', 'Layanan tidak valid.');
        }

        $userLat = (float) $validated['latitude'];
        $userLon = (float) $validated['longitude'];

        // Cari rekomendasi tukang
        $rankedWorkers = $this->workerScoringService->rankWorkers($service->nama, $userLat, $userLon);

        if (empty($rankedWorkers)) {
            return back()->withInput()->with('error', 'Belum ada tukang yang tersedia dalam radius 15 km dari lokasi Anda.');
        }

        // Hitung estimasi harga resmi dari server
        $closestDistance = $rankedWorkers[0]['jarak_km'];
        $durasiDasar = isset($service->durasi_dasar_menit) ? (int) $service->durasi_dasar_menit : 60;

        $calc = $this->pricingService->calculate(
            (float) $service->harga_min,
            (float) $service->harga_max,
            $durasiDasar,
            $validated['kompleksitas'],
            $validated['urgensi'],
            $closestDistance
        );

        // Upload foto
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $fotoPaths[] = $foto->store('request_foto', 'public');
            }
        }

        // Insert ke tabel request
        $idRequest = DB::table('request')->insertGetId([
            'id_user' => Auth::id() ?? 1,
            'layanan' => $service->nama,
            'lokasi' => $validated['lokasi'],
            'latitude' => $userLat,
            'longitude' => $userLon,
            'kompleksitas' => $calc['kompleksitas'],
            'urgensi' => $calc['urgensi'],
            'detail' => $validated['detail'],
            'jarak_km' => $calc['jarak_km'],
            'harga_layanan' => $calc['harga_layanan'],
            'faktor_urgensi' => $calc['faktor_urgensi'],
            'biaya_jarak' => $calc['biaya_jarak'],
            'estimasi_total' => $calc['estimasi_total'],
            'estimasi_durasi_menit' => $calc['estimasi_durasi_menit'],
            'skor_tukang' => $rankedWorkers[0]['skor_total'] ?? null,
            'status' => 'menunggu',
            'status_admin' => 'belum_diterima',
            'tanggal' => now(),
            'foto' => implode(',', $fotoPaths),
            'jumlah_pembayaran' => $calc['estimasi_total'],
            'bayar_sekarang' => 0,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Generate kode booking
        $kodeBooking = 'BK-' . str_pad($idRequest, 3, '0', STR_PAD_LEFT);
        DB::table('request')->where('id_request', $idRequest)->update(['kode_booking' => $kodeBooking]);

        // Simpan hasil ranking maksimal 3 tukang ke tabel rekomendasi_tukang
        $this->workerScoringService->saveRecommendations($idRequest, $rankedWorkers);

        return redirect()->route('bookinguser')->with('success', 'Permintaan layanan berhasil dibuat! Estimasi: Rp ' . number_format($calc['estimasi_total'], 0, ',', '.') . ' (' . $calc['estimasi_durasi_menit'] . ' menit).');
    }
}

