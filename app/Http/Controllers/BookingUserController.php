<?php
namespace App\Http\Controllers;

use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class BookingUserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $requests = \App\Models\Request::where('id_user', $user->id)
            ->with(['user', 'worker', 'preferredWorker', 'estimateWorker', 'offers.worker.reviews', 'selectedOffer.worker', 'review'])
            ->orderByDesc('created_at')
            ->get();
        return view('user.bookinguser', compact('requests'));
    }

    public function pilihPenawaran(HttpRequest $request, $id)
    {
        $validated = $request->validate([
            'offer_id' => 'required|integer|exists:worker_offers,id',
        ]);

        $selected = DB::transaction(function () use ($id, $validated) {
            $booking = DB::table('request')
                ->where('id_request', $id)
                ->where('id_user', auth()->id())
                ->lockForUpdate()
                ->first();

            if (!$booking
                || $booking->status !== 'menunggu_persetujuan'
                || $booking->penawaran_dikirim_at === null
                || $booking->persetujuan_user_at !== null) {
                return false;
            }

            $offer = DB::table('worker_offers')
                ->where('id', $validated['offer_id'])
                ->where('id_request', $id)
                ->where('status', 'ditawarkan')
                ->lockForUpdate()
                ->first();
            if (!$offer) {
                return false;
            }

            DB::table('request')->where('id_request', $id)->update([
                'status' => 'persetujuan_dikirim',
                'id_penawaran_terpilih' => $offer->id,
                'persetujuan_user_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('worker_offers')->where('id', $offer->id)->update([
                'status' => 'dipilih',
                'dipilih_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('worker_offers')->where('id_request', $id)
                ->where('id', '<>', $offer->id)
                ->where('status', 'ditawarkan')
                ->update(['status' => 'tidak_dipilih', 'updated_at' => now()]);

            return true;
        });

        if (!$selected) {
            return redirect()->route('bookinguser')->with('error', 'Penawaran tidak tersedia atau request sudah diproses.');
        }

        return redirect()->route('bookinguser')->with('success', 'Pilihan tukang dan persetujuan harga dikirim kepada admin untuk diverifikasi.');
    }

    public function beriUlasan(HttpRequest $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'ulasan' => 'nullable|string|max:2000',
        ]);

        $created = DB::transaction(function () use ($id, $validated) {
            $booking = DB::table('request')->where('id_request', $id)
                ->where('id_user', auth()->id())->lockForUpdate()->first();
            if (!$booking || $booking->status !== 'selesai' || !$booking->id_worker
                || DB::table('worker_reviews')->where('id_request', $id)->exists()) {
                return false;
            }

            $worker = DB::table('workers')->where('id', $booking->id_worker)->lockForUpdate()->first();
            if (!$worker) {
                return false;
            }

            DB::table('worker_reviews')->insert([
                'id_request' => $id,
                'id_worker' => $booking->id_worker,
                'id_user' => auth()->id(),
                'rating' => $validated['rating'],
                'ulasan' => $validated['ulasan'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $average = DB::table('worker_reviews')->where('id_worker', $booking->id_worker)->avg('rating');
            DB::table('workers')->where('id', $booking->id_worker)->update([
                'rating' => round((float) $average, 2),
                'poin' => min(100, max(0, (int) ($worker->poin ?? 0)) + ($validated['rating'] * 4)),
                'updated_at' => now(),
            ]);

            return true;
        });

        if (!$created) {
            return redirect()->route('bookinguser')->with('error', 'Ulasan hanya dapat diberikan satu kali setelah pekerjaan selesai.');
        }

        return redirect()->route('bookinguser')->with('success', 'Terima kasih, ulasan Anda tersimpan.');
    }

    public function batal(HttpRequest $request, $id)
    {
        $cancelled = DB::transaction(function () use ($id) {
            $booking = DB::table('request')
                ->where('id_request', $id)
                ->where('id_user', auth()->id())
                ->lockForUpdate()
                ->first();

            if (!$booking
                || !in_array($booking->status, ['menunggu', 'menunggu_persetujuan'], true)
                || $booking->status_admin !== 'belum_diterima') {
                return false;
            }

            DB::table('request')->where('id_request', $id)->update([
                'status' => 'dibatalkan',
                'status_admin' => 'dibatalkan',
                'updated_at' => now(),
            ]);

            return true;
        });

        if (!$cancelled) {
            return redirect()->route('bookinguser')->with('error', 'Pesanan hanya dapat dibatalkan sebelum persetujuan penawaran dikirim.');
        }

        return redirect()->route('bookinguser')->with('success', 'Booking berhasil dibatalkan!');
    }
}
