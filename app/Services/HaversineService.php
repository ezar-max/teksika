<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HaversineService
{
    const EARTH_RADIUS_KM = 6371;
    const OFFICE_LATITUDE = -6.4239;
    const OFFICE_LONGITUDE = 106.9401;

    /**
     * Hitung jarak garis lurus antara dua titik koordinat bumi (km)
     * Menggunakan rumus trigonometri Haversine (Great-Circle Distance).
     *
     * @param float $lat1 Latitude titik 1
     * @param float $lon1 Longitude titik 1
     * @param float $lat2 Latitude titik 2
     * @param float $lon2 Longitude titik 2
     * @param int $decimals Jumlah angka di belakang koma
     * @return float Jarak garis lurus dalam kilometer
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2, int $decimals = 2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = self::EARTH_RADIUS_KM * $c;

        return round($distance, $decimals);
    }

    /**
     * Ambil jarak, durasi, dan geometri jalan dari OSRM public demo.
     * Hasil null berarti rute jalan tidak dapat dihitung.
     */
    public function getDrivingRoute(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2,
        float $speedKmh = 30
    ): ?array {
        $cacheKey = sprintf('osrm_route_%.5f_%.5f_to_%.5f_%.5f', $lat1, $lon1, $lat2, $lon2);
        $cachedRoute = Cache::get($cacheKey);
        if ($cachedRoute !== null) {
            return $cachedRoute;
        }

        if (!Cache::add('osrm_public_demo_request', true, 1)) {
            return null;
        }

        $route = $this->fetchOsrmRoute($lat1, $lon1, $lat2, $lon2);
        if ($route !== null) {
            Cache::put($cacheKey, $route, 86400);
        }

        return $route;
    }

    /**
     * Request rute jalan nyata ke Open Source Routing Machine (OSRM)
     */
    protected function fetchOsrmRoute(float $lat1, float $lon1, float $lat2, float $lon2): ?array
    {
        try {
            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%.6f,%.6f;%.6f,%.6f',
                $lon1, $lat1, $lon2, $lat2
            );

            $response = Http::withHeaders([
                'User-Agent' => 'TEKSIKA/1.0 (Laravel routing client)',
            ])->timeout(8)->get($url, [
                'overview' => 'full',
                'geometries' => 'geojson',
                'steps' => 'false',
                'alternatives' => 'false',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['code'] ?? null) === 'Ok' && !empty($data['routes'][0]['geometry']['coordinates'])) {
                    $route = $data['routes'][0];
                    $distanceMeters = (float) $route['distance'];
                    $durationSeconds = (float) $route['duration'];

                    return [
                        'distance_km' => round($distanceMeters / 1000, 2),
                        'duration_minutes' => (int) round($durationSeconds / 60),
                        'geometry' => $route['geometry']['coordinates'],
                        'source' => 'osrm',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::info('OSRM public route request failed.');
        }

        return null;
    }

    /**
     * Estimasi waktu tempuh perjalanan dalam satuan menit.
     * Standar kecepatan di perkotaan = 30 km/jam (2 menit per km).
     *
     * @param float $distanceKm Jarak dalam kilometer
     * @param float $speedKmh Kecepatan dalam km/jam (default 30)
     * @return int Durasi tempuh dalam menit
     */
    public function estimateTravelTimeMinutes(float $distanceKm, float $speedKmh = 30): int
    {
        if ($speedKmh <= 0) {
            $speedKmh = 30;
        }

        $minutes = ($distanceKm / $speedKmh) * 60;
        return (int) round($minutes);
    }
}

