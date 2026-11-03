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
        $requests = \App\Models\Request::with('user', 'worker')->get();
        $workers = \DB::table('workers')->where('status', 'aktif')->get(); // ambil dari tabel workers

        return view('admin.booking', compact('totalBooking', 'waitingBooking', 'requests', 'workers'));
    }

    // Update status booking (diterima / belum_diterima)
    public function updateStatus(Request $request, $id)
    {
        $booking = RequestModel::findOrFail($id);
        $booking->status_admin = $request->input('status_admin');
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
        $req = \App\Models\Request::findOrFail($id);

        // Validasi: worker wajib, jumlah_pembayaran wajib jika bukan COD
        $rules = [
            'id_worker' => 'required|exists:workers,id',
        ];
        if ($req->metode_pembayaran !== 'COD') {
            $rules['jumlah_pembayaran'] = 'required|numeric|min:0';
        } else {
            $rules['jumlah_pembayaran'] = 'nullable|numeric|min:0';
        }
        $validated = $request->validate($rules);

        $req->id_worker = $validated['id_worker'];
        if ($req->metode_pembayaran !== 'COD') {
            $req->jumlah_pembayaran = $validated['jumlah_pembayaran'];
        }
        $req->status_admin = 'diterima';
        $req->save();

        // Set status worker menjadi "lagi bekerja"
        \DB::table('workers')->where('id', $validated['id_worker'])->update(['status' => 'lagi bekerja']);

        return redirect()->route('booking')->with('success', 'Booking dikonfirmasi!');
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