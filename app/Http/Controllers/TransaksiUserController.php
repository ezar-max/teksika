<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class TransaksiUserController extends Controller // <-- Ubah di sini
{
    public function indexAdmin()
    {
        $transaksis = Transaksi::all();
        return view('transaksi', compact('transaksis')); // ini buat admin
    }

    public function indexUser()
    {
        $payments = \App\Models\Pembayaran::where('user_id', auth()->id())->get();
        return view('user.transaksi_user', compact('payments')); // ini buat user
    }
}

