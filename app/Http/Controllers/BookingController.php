<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;

class BookingController extends Controller
{
    public function index()
    {
        $totalBooking = RequestModel::count();
        $waitingBooking = RequestModel::where('status_admin', 'belum_diterima')->count();
        $requests = RequestModel::with('user')->get();

        return view('admin.booking', compact('totalBooking', 'waitingBooking', 'requests'));
    }
}