<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WorkerScoringService;
use App\Services\PricingService;

class BookingController extends Controller
{
    protected WorkerScoringService $scoringService;
    protected PricingService $pricingService;

    public function __construct(WorkerScoringService $scoringService, PricingService $pricingService)
    {
        $this->scoringService = $scoringService;
        $this->pricingService = $pricingService;
    }

    /**
     * Tampilkan daftar booking untuk admin
     */
    public function index()
    {
        $totalBooking = DB::table('request')->count();
        $waitingBooking = DB::table('request')->where('status_admin', 'belum_diterima')->count();
        $requests = DB::table('request')
            ->leftJoin('users', 'request.id_user', '=', 'users.id')
            ->leftJoin('workers', 'request.id_worker', '=', 'workers.id')
            ->select(
                'request.*',
                'users.name as user_name',
                'users.email as user_email',
                'workers.name as worker_name'
            )
            ->orderBy('request.created_at', 'desc')
            ->get();

        $workers = DB::table('workers')->where('status', 'aktif')->get();

        return view('admin.booking', compact('totalBooking', 'waitingBooking', 'requests', 'workers'));
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
            ->orderBy('rekomendasi_tukang.ranking', 'asc')
            ->get();

        // Jika belum ada rekomendasi dan koordinat ada, hitung otomatis
        if ($rekomendasi->isEmpty() && !empty($booking->latitude) && !empty($booking->longitude)) {
            $ranked = $this->scoringService->rankWorkers($booking->layanan, (float)$booking->latitude, (float)$booking->longitude);
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
                    ->orderBy('rekomendasi_tukang.ranking', 'asc')
                    ->get();
            }
        }

        $allWorkers = DB::table('workers')->where('status', 'aktif')->get();

        return view('admin.booking.show', compact('booking', 'rekomendasi', 'allWorkers'));
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

        $ranked = $this->scoringService->rankWorkers($booking->layanan, (float)$booking->latitude, (float)$booking->longitude);

        if (empty($ranked)) {
            return back()->with('warning', 'Tidak ditemukan tukang yang memenuhi kriteria dalam radius 15 km.');
        }

        $this->scoringService->saveRecommendations((int)$booking->id_request, $ranked);

        return back()->with('success', 'Rekomendasi tukang berhasil dihitung ulang!');
    }

    /**
     * Penugasan Tukang (Tugaskan) oleh Admin
     * Mengubah id_worker, status menjadi dikonfirmasi, status_admin menjadi diterima
     */
    public function tugaskan(Request $request, $id)
    {
        $request->validate([
            'id_worker' => 'required|exists:workers,id',
        ]);

        $idWorker = $request->input('id_worker');

        $booking = DB::table('request')->where('id_request', $id)->first();
        if (!$booking) {
            return back()->with('error', 'Booking tidak ditemukan.');
        }

        // Update booking status
        DB::table('request')->where('id_request', $id)->update([
            'id_worker' => $idWorker,
            'status' => 'dikonfirmasi',
            'status_admin' => 'diterima',
            'updated_at' => now(),
        ]);

        return redirect()->route('booking')->with('success', 'Tukang berhasil ditugaskan! Status booking telah dikonfirmasi.');
    }

    /**
     * Tolak Booking
     */
    public function tolak($id)
    {
        DB::table('request')->where('id_request', $id)->update([
            'status' => 'dibatalkan',
            'status_admin' => 'ditolak',
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Booking berhasil ditolak.');
    }
}

