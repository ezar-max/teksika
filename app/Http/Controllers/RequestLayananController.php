<?php
namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class RequestLayananController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $services = Service::all(); // atau model layanan yang kamu punya
        return view('user.request', compact('user', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // ...validasi lain...
            'metode_pembayaran' => 'required',
            'bukti_pembayaran' => [
                'nullable',
                'image',
                'max:5120',
                function($attribute, $value, $fail) use ($request) {
                    if (in_array($request->metode_pembayaran, ['Bank', 'E-wallet']) && !$request->hasFile('bukti_pembayaran')) {
                        $fail('Bukti pembayaran wajib diupload untuk Bank/E-wallet.');
                    }
                }
            ],
        ]);
        // Simpan file jika ada
        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }
        // Simpan request ke database (tambahkan field bukti_pembayaran)
        // Simpan booking tanpa kode_booking dulu
        $booking = \App\Models\Request::create([
            // ...field lain...
            'id_user' => auth()->id(),
            'layanan' => $request->layanan,
            'lokasi' => $request->lokasi,
            'urgensi' => $request->urgensi,
            'detail' => $request->detail,
            'status' => 'sedang',
            'status_admin' => 'belum_diterima',
            'tanggal' => now(),
            'jumlah_pembayaran' => $request->jumlah_pembayaran,
            'bayar_sekarang' => $request->bayar_sekarang ? 1 : 0,
            'metode_pembayaran' => $request->metode_pembayaran,
            // ...dst
        ]);

        // Generate kode booking otomatis
        $kode = 'BK-' . str_pad($booking->id_request, 3, '0', STR_PAD_LEFT);
        $booking->kode_booking = $kode;
        $booking->save();

        return redirect()->route('bookinguser')->with('success', 'Request berhasil dikirim!');
    }
}