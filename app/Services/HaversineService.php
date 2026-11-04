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
     * Ambil jarak, durasi, dan geometri jalan dari Google Maps Compute Routes (utama)
     * dengan fallback otomatis ke OSRM public demo.
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

        // 1. Provider Utama: Google Maps Routes API (Compute Routes)
        $route = $this->fetchGoogleDrivingRoute($lat1, $lon1, $lat2, $lon2);
        if ($route !== null) {
            Cache::put($cacheKey, $route, 86400);
            return $route;
        }

        // 2. Fallback: Open Source Routing Machine (OSRM)
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
     * Request rute jalan ke Google Maps Routes API (v2:computeRoutes)
     */
    protected function fetchGoogleDrivingRoute(float $lat1, float $lon1, float $lat2, float $lon2): ?array
    {
        $apiKey = trim((string) config('services.google_maps.api_key', ''));
        if ($apiKey === '') {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline',
            ])->timeout(8)->post('https://routes.googleapis.com/directions/v2:computeRoutes', [
                'origin' => [
                    'location' => [
                        'latLng' => [
                            'latitude' => $lat1,
                            'longitude' => $lon1,
                        ],
                    ],
                ],
                'destination' => [
                    'location' => [
                        'latLng' => [
                            'latitude' => $lat2,
                            'longitude' => $lon2,
                        ],
                    ],
                ],
                'travelMode' => 'DRIVE',
                'routingPreference' => 'TRAFFIC_UNAWARE',
                'computeAlternativeRoutes' => false,
            ]);

            if ($response->successful()) {
                $route = $response->json('routes.0');
                $encodedPolyline = $route['polyline']['encodedPolyline'] ?? null;
                $distanceMeters = $route['distanceMeters'] ?? null;
                $durationRaw = $route['duration'] ?? null;

                if (is_numeric($distanceMeters) && is_string($encodedPolyline) && $encodedPolyline !== '') {
                    $durationSeconds = is_string($durationRaw)
                        ? (float) rtrim($durationRaw, 's')
                        : (float) ($durationRaw ?? 0);
                    $geometry = $this->decodeGooglePolyline($encodedPolyline);

                    if (!empty($geometry)) {
                        return [
                            'distance_km' => round(((float) $distanceMeters) / 1000, 2),
                            'duration_minutes' => max(1, (int) round($durationSeconds / 60)),
                            'geometry' => $geometry,
                            'encoded_polyline' => $encodedPolyline,
                            'source' => 'google_routes',
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::info('Google Maps Compute Routes request failed, falling back to OSRM.');
        }

        return null;
    }

    /**
     * Decode Google Encoded Polyline string menjadi array koordinat [lon, lat]
     */
    public function decodeGooglePolyline(string $encoded): array
    {
        $points = [];
        $index = 0;
        $len = strlen($encoded);
        $lat = 0;
        $lng = 0;

        while ($index < $len) {
            $shift = 0;
            $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20 && $index < $len);
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            if ($index >= $len) {
                break;
            }

            $shift = 0;
            $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20 && $index < $len);
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            $points[] = [round($lng * 1e-5, 6), round($lat * 1e-5, 6)];
        }

        return $points;
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

