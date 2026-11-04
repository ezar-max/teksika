<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserAuthController extends Controller
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

        $user = User::where('email', $email)->first();
        if (!$user) {
            // Check if admin is logging in via customer tab
            $admin = \App\Models\Admin::where('email', $email)->first();
            if ($admin && Hash::check($password, $admin->password)) {
                Auth::guard('web')->logout();
                Auth::guard('admin')->login($admin);
                $request->session()->regenerate();
                session(['role' => 'admin']);
                return redirect()->route('dashboard_admin');
            }
            return back()->withInput()->with(['login_error' => 'notfound', 'active_tab' => 'customer']);
        }

        if (!Hash::check($password, $user->password)) {
            return back()->withInput()->with(['login_error' => 'wrong', 'active_tab' => 'customer']);
        }

        // If the user's role is admin, redirect to admin dashboard
        if ($user->role === 'admin') {
            $admin = \App\Models\Admin::where('email', $email)->first();
            if (!$admin) {
                $admin = \App\Models\Admin::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                ]);
            }
            Auth::guard('web')->logout();
            Auth::guard('admin')->login($admin);
            Auth::guard('web')->login($user);
            $request->session()->regenerate();
            session(['role' => 'admin']);
            return redirect()->route('dashboard_admin');
        }

        Auth::guard('admin')->logout();
        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        session(['role' => 'customer']);
        return redirect()->route('dashboard_user');
    }

    public function showRegister()
    {
        return view('login.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'customer'
        ]);

        Auth::login($user);
        session(['role' => 'customer']);
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }

    public function dashboard()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with(['login_error' => 'auth_required', 'active_tab' => 'customer']);
        }
        $services = \App\Models\Service::all();
        $requests = \App\Models\Request::where('id_user', $user->id)->orderBy('created_at', 'desc')->get();
        $totalBooking = $requests->count();
        $totalTransaksi = \App\Models\Pembayaran::where('user_id', $user->id)->count();

        return view('dashboard_user', compact('user', 'services', 'totalBooking', 'totalTransaksi', 'requests'));
    }

    public function bookinguser()
    {
        $user = auth()->user(); // Ambil data user yang sedang login
        return view('user.bookinguser', compact('user'));
    }

    public function chatuser()
    {
        $user = auth()->user();
        return view('user.chatuser', compact('user'));
    }

    public function request()
    {
        $user = auth()->user();
        return view('user.request', compact('user'));
    }

    public function transaksi_user()
    {
        $user = auth()->user();
        return view('user.transaksi_user', compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'avatar' => 'nullable|image|max:5120', // max 5MB
        ]);
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if (!empty($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path; // simpan path relatif
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diupdate!');
    }
}
