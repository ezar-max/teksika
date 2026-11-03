<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;
use App\Models\Booking;
use App\Models\Worker;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('login.menu_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        $admin = Admin::where('email', $email)->first();
        $user = User::where('email', $email)->first();

        // If email not found in either admins or users
        if (!$admin && !$user) {
            return back()->withInput()->with([
                'login_error' => 'notfound',
                'active_tab' => 'admin'
            ]);
        }

        // If found in users but role is customer
        if (!$admin && $user && $user->role !== 'admin') {
            return back()->withInput()->with([
                'login_error' => 'is_customer',
                'active_tab' => 'admin'
            ]);
        }

        // Validate password
        $passwordValid = false;
        if ($admin && Hash::check($password, $admin->password)) {
            $passwordValid = true;
        } elseif ($user && $user->role === 'admin' && Hash::check($password, $user->password)) {
            $passwordValid = true;
            // Sync to admins table if not yet present
            if (!$admin) {
                $admin = Admin::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                ]);
            }
        }

        if (!$passwordValid) {
            return back()->withInput()->with([
                'login_error' => 'wrong',
                'active_tab' => 'admin'
            ]);
        }

        // Ensure $admin instance is loaded
        if (!$admin) {
            $admin = Admin::where('email', $email)->first();
        }

        Auth::guard('admin')->login($admin);
        if ($user) {
            Auth::guard('web')->login($user);
        }
        session(['role' => 'admin']);

        return redirect()->route('dashboard_admin');
    }

    public function index()
    {
        $bookingCount = Booking::count();
        $workerActiveCount = Worker::where('status', 'aktif')->count();

        return view('admin.dashboard', compact('bookingCount', 'workerActiveCount'));
    }

    public function dashboard()
    {
        return view('dashboard_admin');
    }
}
