<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingAdminController;
use App\Http\Controllers\BookingUserController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\TransaksiUserController;
use App\Http\Controllers\BookingExportController;
use App\Http\Controllers\WorkerController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Welcome / Splash Screen (Show once per browser session)
Route::get('/', function () {
    if (session('splash_seen')) {
        return redirect()->route('landingpageuser');
    }
    session(['splash_seen' => true]);
    return view('welcome');
})->name('splash');

// Landing Page
Route::get('/landingpageuser', [AuthController::class, 'landingpageuser'])->name('landingpageuser');

// Authentication Routes
Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [UserAuthController::class, 'login'])->name('login.post');
Route::get('/login/menu_login', [AuthController::class, 'menuLogin'])->name('menu_login');
Route::get('/menu_login', function () {
    return redirect()->route('login');
});

// Admin Login Post
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');

// Registration Routes
Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [UserAuthController::class, 'register'])->name('register.post');
Route::get('/login/register', [UserAuthController::class, 'showRegister'])->name('register_form');
Route::post('/login/register', [UserAuthController::class, 'register']);

// Unified Logout Route (Handles both POST and GET)
Route::match(['get', 'post'], '/logout', function (\Illuminate\Http\Request $request) {
    Auth::guard('web')->logout();
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
})->name('logout');


/*
|--------------------------------------------------------------------------
| Customer / User Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['customer.auth'])->group(function () {
    Route::get('/dashboard_user', [UserAuthController::class, 'dashboard'])->name('dashboard_user');
    Route::get('/dashboard-user', [DashboardUserController::class, 'index']);

    // Booking & Requests
    Route::get('/booking-user', [BookingUserController::class, 'index'])->name('bookinguser');
    Route::post('/booking-user/{id}/batal', [BookingUserController::class, 'batal'])->name('bookinguser.batal');

    Route::get('/user/request', [AuthController::class, 'request'])->name('request');
    Route::get('/request', [RequestController::class, 'index'])->name('request.page');
    Route::post('/request', [RequestController::class, 'store'])->name('request.store');

    Route::get('/request-layanan', [App\Http\Controllers\RequestLayananController::class, 'index'])->name('request.layanan');
    Route::post('/request-layanan', [RequestController::class, 'store']);

    // Chat
    Route::get('/user/chatuser', [AuthController::class, 'chatuser'])->name('chatuser');
    Route::get('/chatuser', [ChatController::class, 'chatuser']);
    Route::post('/chatuser/send', [ChatController::class, 'store'])->name('chatuser.send');

    // Profile & Transactions
    Route::get('/user/profil_user', [AuthController::class, 'profil_user'])->name('profil_user');
    Route::put('/user/update-profile', [UserAuthController::class, 'updateProfile'])->name('user.updateProfile');
    Route::get('/user/transaksi_user', [TransaksiUserController::class, 'indexUser'])->name('transaksi_user');

    // Payments
    Route::get('/pembayaran', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/{kode}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::put('/pembayaran/{kode}', [PembayaranController::class, 'update'])->name('pembayaran.update');
    Route::match(['get', 'post'], '/request/preview', [PembayaranController::class, 'preview'])->name('request.preview');
    Route::post('/request/payment', [RequestController::class, 'payment'])->name('request.payment');
});


/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/dashboard_admin', [ServiceController::class, 'index'])->name('dashboard_admin');
    Route::get('/admin/home', [ServiceController::class, 'index'])->name('admin.home');
    Route::get('/admin/dashboard', [AdminAuthController::class, 'index'])->name('dashboardadmin');

    // Services Management
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // Bookings Management
    Route::get('/admin/booking', [BookingController::class, 'index'])->name('booking');
    Route::get('/booking', [BookingAdminController::class, 'index']);
    Route::get('/admin/bookingadmin', [BookingAdminController::class, 'index'])->name('bookingadmin');
    Route::get('/admin/booking/{id}', [BookingAdminController::class, 'show'])->name('booking.detail');
    Route::post('/admin/booking/{id}/status', [BookingAdminController::class, 'updateStatus'])->name('booking.status');
    Route::post('/admin/booking/{id}/terima', [BookingAdminController::class, 'terima'])->name('booking.terima');
    Route::post('/admin/booking/{id}/konfirmasi', [BookingAdminController::class, 'konfirmasi'])->name('booking.konfirmasi');
    Route::post('/admin/booking/{id}/tolak', [BookingAdminController::class, 'tolak'])->name('booking.tolak');
    Route::get('/admin/booking/export/pdf', [BookingAdminController::class, 'exportPdf'])->name('booking.export.pdf');
    Route::get('/export-booking-pdf', [BookingExportController::class, 'exportPDF'])->name('export.booking.pdf');

    // Transactions Management
    Route::get('/admin/transaksi', [App\Http\Controllers\Admin\TransaksiController::class, 'index'])->name('transaksi');

    // Chat Management
    Route::get('/admin/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/admin/chat/send', [ChatController::class, 'send'])->name('chat.send');

    // Workers Management
    Route::resource('worker', WorkerController::class);
    Route::get('/admin/worker', [WorkerController::class, 'index'])->name('worker');
    Route::post('/admin/worker', [WorkerController::class, 'store'])->name('worker.store');
    Route::put('/admin/worker/{id}', [WorkerController::class, 'update'])->name('worker.update');
    Route::delete('/admin/worker/{id}', [WorkerController::class, 'destroy'])->name('worker.destroy');
});
