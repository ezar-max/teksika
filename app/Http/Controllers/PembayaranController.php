<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    // Form pembayaran
    public function create(Request $request)
    {
        $kode = $request->query('booking');
        if ($kode) {
            $booking = \App\Models\Request::where('kode_booking', $kode)
                ->where('id_user', auth()->id())
                ->where('status_admin', 'diterima')
                ->first();

            if (!$booking) {
                abort(404, 'Booking tidak ditemukan atau belum dikonfirmasi admin.');
            }
            if ((int) $booking->bayar_sekarang === 1) {
                return redirect()->route('bookinguser')->with('success', 'Booking ini sudah dibayar.');
            }
            if ($booking->status !== 'dikonfirmasi' || (float) $booking->jumlah_pembayaran <= 0) {
                return redirect()->route('bookinguser')->with('error', 'Pembayaran belum tersedia untuk booking ini.');
            }

            $requestData = [
                'kode_booking' => $booking->kode_booking,
                'layanan' => $booking->layanan,
                'lokasi' => $booking->lokasi,
                'urgensi' => $booking->urgensi,
                'detail' => $booking->detail,
                'jumlah_pembayaran' => $booking->jumlah_pembayaran,
                'metode_pembayaran' => $booking->metode_pembayaran,
                'bayar_sekarang' => 1,
            ];
            $fotoPaths = [];
            if (!empty($booking->foto)) {
                $fotoPaths = array_filter(array_map('trim', explode(',', $booking->foto)));
            }
            $jumlah = $booking->jumlah_pembayaran;
            return view('pembayaran.create', compact('requestData', 'fotoPaths', 'jumlah'));
        }
        return view('pembayaran.create');
    }

    // Proses pembayaran
    public function store(Request $request)
    {
        // Jika pembayaran berasal dari booking yang sudah ada (kode booking disertakan), hanya buat record pembayaran
        if ($request->filled('booking_kode')) {
            $validated = $request->validate([
                'booking_kode' => 'required|string',
                'jumlah' => 'required|numeric|min:0.01',
                'metode_pembayaran' => 'required|string|max:20',
                'nama_pembayar' => 'nullable|string|max:255',
                'email_pembayar' => 'nullable|email',
                'keterangan' => 'nullable|string',
            ]);

            $paymentResult = DB::transaction(function () use ($validated) {
                $booking = DB::table('request')
                    ->where('kode_booking', $validated['booking_kode'])
                    ->where('id_user', auth()->id())
                    ->lockForUpdate()
                    ->first();

                if (!$booking || $booking->status_admin !== 'diterima') {
                    return 'unavailable';
                }
                if ((int) $booking->bayar_sekarang === 1) {
                    return 'already_paid';
                }
                if ($booking->status !== 'dikonfirmasi' || (float) $booking->jumlah_pembayaran <= 0) {
                    return 'unavailable';
                }
                if (round((float) $validated['jumlah'], 2) !== round((float) $booking->jumlah_pembayaran, 2)) {
                    return 'amount_mismatch';
                }

                Pembayaran::create([
                    'kode_pembayaran' => Pembayaran::generateKode(),
                    'jumlah' => $booking->jumlah_pembayaran,
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'nama_pembayar' => $validated['nama_pembayar'] ?? (auth()->user()->name ?? 'Pelanggan'),
                    'email_pembayar' => $validated['email_pembayar'] ?? null,
                    'keterangan' => $validated['keterangan'] ?? 'Pembayaran booking ' . $booking->kode_booking,
                    'status' => 'success',
                    'user_id' => auth()->id(),
                ]);

                DB::table('request')->where('id_request', $booking->id_request)->update([
                    'bayar_sekarang' => 1,
                    'updated_at' => now(),
                ]);

                return 'paid';
            });

            if ($paymentResult === 'already_paid') {
                return redirect()->route('bookinguser')->with('success', 'Booking ini sudah dibayar.');
            }
            if ($paymentResult === 'amount_mismatch') {
                return back()->withInput()->with('error', 'Jumlah pembayaran tidak sesuai dengan total yang disetujui admin.');
            }
            if ($paymentResult !== 'paid') {
                return redirect()->route('bookinguser')->with('error', 'Pembayaran hanya tersedia untuk booking yang sudah dikonfirmasi admin.');
            }

            return redirect()->route('bookinguser')->with('success', 'Pembayaran berhasil. Admin dapat memulai pengerjaan.');
        }

        // Alur lama (preview -> create): tetap dipertahankan
        $validated = $request->validate([
            'layanan' => 'required|string|max:50',
            'lokasi' => 'required|string|max:100',
            'urgensi' => 'required|in:rendah,sedang,tinggi',
            'detail' => 'required|string',
            'jumlah_pembayaran' => 'required|numeric|min:0',
            'bayar_sekarang' => 'nullable',
            'metode_pembayaran' => 'required|string|max:20',
            'jumlah' => 'required|numeric|min:0',
            'nama_pembayar' => 'nullable|string|max:255',
            'email_pembayar' => 'nullable@Email',
            'keterangan' => 'nullable|string',
            'fotoPaths' => 'nullable|array',
            'fotoPaths.*' => 'string',
        ]);

        $kodePembayaran = \App\Models\Pembayaran::generateKode();
        \App\Models\Pembayaran::create([
            'kode_pembayaran' => $kodePembayaran,
            'jumlah' => $validated['jumlah'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'nama_pembayar' => $validated['nama_pembayar'] ?? 'QRIS',
            'email_pembayar' => $validated['email_pembayar'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => 'success',
            'user_id' => auth()->id(),
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

        // Simpan request (alur lama)
        $requestModel = \App\Models\Request::create([
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

        $kode = 'BK-' . str_pad($requestModel->id_request, 3, '0', STR_PAD_LEFT);
        $requestModel->kode_booking = $kode;
        $requestModel->save();

        return redirect()->route('bookinguser')->with('success', 'Pembayaran & request berhasil!');
    }

    // Detail pembayaran
    public function show($kode)
    {
        $pembayaran = Pembayaran::where('kode_pembayaran', $kode)->firstOrFail();
        return view('pembayaran.show', compact('pembayaran'));
    }

    public function update(Request $request, $kode)
    {
        $pembayaran = Pembayaran::where('kode_pembayaran', $kode)->firstOrFail();
        
        $validated = $request->validate([
            'status' => 'required|in:success,failed'
        ]);
        
        $pembayaran->update([
            'status' => $validated['status']
        ]);
        
        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui!');
    }

    public function preview(Request $request)
    {
        if ($request->isMethod('get')) {
            // Jika GET, tampilkan halaman info atau redirect ke form request
            return redirect()->route('request')->with('info', 'Silakan isi form request terlebih dahulu.');
        }

        // Jika POST, proses preview pembayaran
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

        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $fotoPaths[] = $foto->store('temp_request_foto', 'public');
            }
        }

        return view('pembayaran.create', [
            'requestData' => $validated,
            'fotoPaths' => $fotoPaths,
            'jumlah' => $validated['jumlah_pembayaran'],
        ]);
    }
}