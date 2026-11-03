<?php
namespace App\Http\Controllers;

use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;

class BookingUserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $requests = \App\Models\Request::where('id_user', $user->id)
            ->with(['user', 'worker']) // tambahkan 'worker'
            ->get();
        return view('user.bookinguser', compact('requests'));
    }

    // Batalkan booking oleh user (hanya jika masih menunggu/belum_diterima)
    public function batal(HttpRequest $request, $id)
    {
        $user = auth()->user();
        $booking = \App\Models\Request::where('id_request', $id)
            ->where('id_user', $user->id)
            ->firstOrFail();

        if ($booking->status_admin !== 'belum_diterima') {
            return redirect()->route('bookinguser')->with('success', 'Booking tidak dapat dibatalkan.');
        }

        $booking->status_admin = 'dibatalkan';
        $booking->save();

        return redirect()->route('bookinguser')->with('success', 'Booking berhasil dibatalkan!');
    }
}
