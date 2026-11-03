<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class AuthController extends Controller
{
    public function menuLogin()
    {
        // Ubah path view sesuai folder baru
        return view('login.menu_login');
    }  

    public function dashboardUser()
    {
        return view('dashboard_user');
    }

    public function dashboarAdmin()
    {
        return view('dashboard_admin');
    }

    public function booking()
    {
        return view('admin.booking');
    }

    public function transaksi()
    {
        return view('admin.transaksi');
    }

    public function worker()
    {
        return view('admin.worker');
    }
    
    public function landingpageuser()
    {
        return view('landingpageuser');
    }

    public function chatuser()
    {
        return view('user.chatuser');
    }
    public function request()
    {
        return view('user.request');
    }

        public function bookinguser()
    {
        return view('user.bookinguser');
    }

       public function transaksi_user()
    {
        return view('user.transaksi_user');
    }

        public function chat()
    {
        return view('admin.chat');
    }
}
