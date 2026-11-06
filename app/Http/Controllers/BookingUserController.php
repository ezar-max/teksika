<?php
namespace App\Http\Controllers;

use App\Models\Request;
use App\Services\HaversineService;
use App\Services\PricingService;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class BookingUserController extends Controller
{
    public function index(HaversineService $haversineService, PricingService $pricingService)
    {
        $user = auth()->user();
        $requests = \App\Models\Request::where('id_user', $user->id)
            ->with(['user', 'worker', 'preferredWorker', 'estimateWorker', 'offers.worker.reviews', 'selectedOffer.worker', 'review'])
            ->orderByDesc('created_at')
            ->get();

        $pricingRules = $pricingService->getRules();
        $freeDistanceKm = (float) ($pricingRules['jarak_gratis_km'] ?? 5);
        $pricePerKm = (float) ($pricingRules['tarif_per_km'] ?? 2500);

        foreach ($requests as $booking) {
            $refreshInitialEstimate = $booking->status === 'menunggu'
                && $booking->offers->isEmpty()
                && $booking->estimasi_total !== null;
            $refreshOpenOffers = $booking->status === 'menunggu_persetujuan'
                && $booking->offers->contains('status', 'ditawarkan');
            if ((!$refreshInitialEstimate && !$refreshOpenOffers)
                || $booking->latitude === null
                || $booking->longitude === null
                || $booking->jarak_km === null) {
                continue;
            }

            $straightDistanceKm = $haversineService->calculateDistance(
                HaversineService::OFFICE_LATITUDE,
                HaversineService::OFFICE_LONGITUDE,
                (float) $booking->latitude,
                (float) $booking->longitude
            );
            if (abs((float) $booking->jarak_km - $straightDistanceKm) > 0.01) {
                continue;
            }

            $route = $haversineService->getDrivingRoute(
                HaversineService::OFFICE_LATITUDE,
                HaversineService::OFFICE_LONGITUDE,
                (float) $booking->latitude,
                (float) $booking->longitude
            );
            if ($route === null) {
                continue;
            }

            $routeDistanceKm = (float) $route['distance_km'];
            $transportFee = $routeDistanceKm > $freeDistanceKm
                ? round(($routeDistanceKm - $freeDistanceKm) * $pricePerKm)
                : 0;
            $routeFields = [
                'jarak_km' => $routeDistanceKm,
                'durasi_perjalanan_menit' => $route['duration_minutes'],
                'updated_at' => now(),
            ];

            if ($refreshInitialEstimate) {
                $routeFields['biaya_jarak'] = $transportFee;
                $routeFields['estimasi_total'] = (float) $booking->estimasi_total
                    - (float) ($booking->biaya_jarak ?? 0)
                    + $transportFee;
                DB::table('request')->where('id_request', $booking->id_request)->update($routeFields);
                $booking->setAttribute('biaya_jarak', $transportFee);
                $booking->setAttribute('estimasi_total', $routeFields['estimasi_total']);
            } elseif ($refreshOpenOffers) {
                DB::table('request')->where('id_request', $booking->id_request)->update($routeFields);
                foreach ($booking->offers->where('status', 'ditawarkan') as $offer) {
                    $previousTransportFee = (float) $offer->biaya_transportasi;
                    $previousTotal = (float) $offer->total_estimasi;
                    $offerTransportFee = $routeDistanceKm > $freeDistanceKm
                        ? round(($routeDistanceKm - $freeDistanceKm) * $pricePerKm)
                        : 0;
                    DB::table('worker_offers')->where('id', $offer->id)->update([
                        'jarak_km' => $routeDistanceKm,
                        'biaya_transportasi' => $offerTransportFee,
                        'total_estimasi' => $previousTotal - $previousTransportFee + $offerTransportFee,
                        'updated_at' => now(),
                    ]);
                    $offer->setAttribute('jarak_km', $routeDistanceKm);
                    $offer->setAttribute('biaya_transportasi', $offerTransportFee);
                    $offer->setAttribute('total_estimasi', $previousTotal - $previousTransportFee + $offerTransportFee);
                }
            } else {
                continue;
            }

            $booking->setAttribute('jarak_km', $routeDistanceKm);
            $booking->setAttribute('durasi_perjalanan_menit', $route['duration_minutes']);
        }

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
