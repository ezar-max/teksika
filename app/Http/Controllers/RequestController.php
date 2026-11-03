<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as HttpRequest;
use App\Models\Request as RequestModel;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function store(HttpRequest $request)
    {
        // Validasi
        $validated = $request->validate([
            'layanan' => 'required|string|max:50',
            'lokasi' => 'required|string|max:255',
            'urgensi' => 'required|in:rendah,sedang,tinggi',
            'detail' => 'required|string',
            'metode_pembayaran' => 'required|in:E-wallet,Bank,COD',
            'foto.*' => 'image|max:5120',
        ]);

        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $fotoPaths[] = $foto->store('request_foto', 'public');
            }
        }

        // Simpan ke database
        $booking = RequestModel::create([
            'id_user' => auth()->id(),
            'layanan' => $validated['layanan'],
            'lokasi' => $validated['lokasi'],
            'urgensi' => $validated['urgensi'],
            'detail' => $validated['detail'],
            'status' => 'sedang',
            'status_admin' => 'belum_diterima',
            'tanggal' => now(),
            'foto' => implode(',', $fotoPaths),
            'jumlah_pembayaran' => null, // diinput admin saat konfirmasi
            'bayar_sekarang' => 0,
            'metode_pembayaran' => $validated['metode_pembayaran'],
        ]);

        // Generate kode booking otomatis
        $kode = 'BK-' . str_pad($booking->id_request, 3, '0', STR_PAD_LEFT);
        $booking->kode_booking = $kode;
        $booking->save();

        return redirect()->route('bookinguser')->with('success', 'Request berhasil dikirim!');
    }

    public function index()
    {
        $requests = Request::where('id_user', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.bookinguser', compact('requests'));
    }

    public function preview(HttpRequest $request)
    {
        // Validasi awal (tanpa simpan DB)
        $validated = $request->validate([
            'layanan' => 'required|string|max:50',
            'lokasi' => 'required|string|max:255',
            'urgensi' => 'required|in:rendah,sedang,tinggi',
            'detail' => 'required|string',
            'jumlah_pembayaran' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:E-wallet,Bank,COD',
            'foto.*' => 'image|max:5120',
            'bayar_sekarang' => 'nullable',
        ]);

        // Simpan file foto ke temporary (atau session, atau re-upload di payment)
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $fotoPaths[] = $foto->store('temp_request_foto', 'public');
            }
        }

        // Ambil jumlah dari input (atau dari objek request booking jika sudah ada)
        $jumlah = $request->jumlah_pembayaran ?? 0;

        // Kirim data ke view pembayaran
        return view('pembayaran.create', [
            'requestData' => $validated,
            'fotoPaths' => $fotoPaths,
            'jumlah' => $jumlah,
        ]);
    }

    public function payment(HttpRequest $request)
    {
        // Validasi ulang + upload ulang foto jika perlu
        $validated = $request->validate([
            'layanan' => 'required|string|max:50',
            'lokasi' => 'required|string|max:255',
            'urgensi' => 'required|in:rendah,sedang,tinggi',
            'detail' => 'required|string',
            'jumlah_pembayaran' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:E-wallet,Bank',
            'bayar_sekarang' => 'nullable',
            'fotoPaths' => 'nullable|array',
            'fotoPaths.*' => 'string',
        ]);

        // Pindahkan foto dari temp ke folder final
        $finalFotoPaths = [];
        if (!empty($validated['fotoPaths'])) {
            foreach ($validated['fotoPaths'] as $tempPath) {
                $filename = basename($tempPath);
                $newPath = 'request_foto/' . $filename;
                \Storage::disk('public')->move($tempPath, $newPath);
                $finalFotoPaths[] = $newPath;
            }
        }

        // Simpan ke database
        $booking = \App\Models\Request::create([
            'id_user' => auth()->id(),
            'layanan' => $validated['layanan'],
            'lokasi' => $validated['lokasi'],
            'urgensi' => $validated['urgensi'],
            'detail' => $validated['detail'],
            'status' => 'sedang',
            'status_admin' => 'belum_diterima',
            'tanggal' => now(),
            'foto' => implode(',', $finalFotoPaths),
            'jumlah_pembayaran' => $validated['jumlah_pembayaran'],
            'bayar_sekarang' => $request->bayar_sekarang ? 1 : 0,
            'metode_pembayaran' => $validated['metode_pembayaran'],
        ]);

        // Generate kode booking otomatis
        $kode = 'BK-' . str_pad($booking->id_request, 3, '0', STR_PAD_LEFT);
        $booking->kode_booking = $kode;
        $booking->save();

        return redirect()->route('bookinguser')->with('success', 'Request berhasil dikirim!');
    }
}
