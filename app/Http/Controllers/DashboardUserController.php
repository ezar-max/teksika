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
    $services = Service::all();
    $requests = \App\Models\Request::where('id_user', $user->id)->orderBy('created_at', 'desc')->get();
    $totalBooking = $requests->count();
    $totalTransaksi = Pembayaran::where('user_id', $user->id)->count();

    return view('dashboard_user', compact('user', 'services', 'totalBooking', 'totalTransaksi', 'requests'));
    }
}