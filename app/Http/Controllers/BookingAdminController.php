<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan di atas

class BookingAdminController extends Controller
{
    // Tampilkan daftar booking
    public function index()
    {
        $totalBooking = \App\Models\Request::count();
        $waitingBooking = \App\Models\Request::where('status_admin', 'belum_diterima')->count();
        $approvalWaiting = \App\Models\Request::where('status', 'persetujuan_dikirim')->count();
        $requests = \App\Models\Request::with('user', 'worker')->get();
        $workers = \DB::table('workers')->where('status', 'aktif')->get(); // ambil dari tabel workers

        return view('admin.booking', compact('totalBooking', 'waitingBooking', 'approvalWaiting', 'requests', 'workers'));
    }

    // Update status booking (diterima / belum_diterima)
    public function updateStatus(Request $request, $id)
    {
        $booking = RequestModel::findOrFail($id);
        $validated = $request->validate([
            'status_admin' => 'required|in:ditolak',
        ]);

        if (!in_array($booking->status, ['menunggu', 'menunggu_persetujuan'], true)
            || $booking->status_admin !== 'belum_diterima') {
            return response()->json(['success' => false, 'message' => 'Status request tidak dapat diubah.'], 409);
        }

        $booking->status_admin = $validated['status_admin'];
        $booking->status = 'ditolak';
        $booking->save();

        return response()->json(['success' => true]);
    }

    // Tampilkan detail booking
    public function show($id)
    {
        $booking = \App\Models\Request::with('user')->findOrFail($id);
        return view('admin.booking_detail', compact('booking'));
    }

    // Konfirmasi booking
    public function konfirmasi(Request $request, $id)
    {
        return app(\App\Http\Controllers\Admin\BookingController::class)->tugaskan($request, $id);
    }

    // Tolak booking
    public function tolak($id)
    {
        $booking = \App\Models\Request::findOrFail($id);
        $booking->status_admin = 'ditolak';
        $booking->save();

        return redirect()->back()->with('success', 'Booking berhasil ditolak!');
    }

    public function exportPdf()
    {
        $requests = \App\Models\Request::with('user', 'worker')->get();

        $pdf = Pdf::loadView('exports.booking_pdf', compact('requests'));
        return $pdf->download('laporan_booking.pdf');
    }
}