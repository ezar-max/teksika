<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;

class DashboardUserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $services = Service::query()
            ->where('status', 'aktif')
            ->get(['id', 'nama', 'deskripsi', 'gambar', 'harga_min', 'harga_max', 'status']);
        $requests = \App\Models\Request::where('id_user', $user->id)
            ->latest('created_at')
            ->limit(10)
            ->get([
                'layanan',
                'status_admin',
                'metode_pembayaran',
                'jumlah_pembayaran',
                'bayar_sekarang',
                'created_at',
                'updated_at',
                'kode_booking',
            ]);
        $totalBooking = \App\Models\Request::where('id_user', $user->id)->count();
        $totalTransaksi = Pembayaran::where('user_id', $user->id)->count();

        return view('dashboard_user', compact('user', 'services', 'totalBooking', 'totalTransaksi', 'requests'));
    }
}