<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        // Ambil data dari tabel pembayarans
        $transaksis = DB::table('pembayarans')->get();
        return view('admin.transaksi', compact('transaksis'));
    }
}
