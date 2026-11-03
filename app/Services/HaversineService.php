<?php

namespace App\Services;

class HaversineService
{
    const EARTH_RADIUS_KM = 6371;

    /**
     * Hitung jarak garis lurus antara dua titik koordinat bumi (km)
     * Menggunakan rumus trigonometri Haversine.
     *
     * @param float $lat1 Latitude titik 1 (user)
     * @param float $lon1 Longitude titik 1 (user)
     * @param float $lat2 Latitude titik 2 (worker/tukang)
     * @param float $lon2 Longitude titik 2 (worker/tukang)
     * @param int $decimals Jumlah angka di belakang koma
     * @return float Jarak dalam kilometer
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

