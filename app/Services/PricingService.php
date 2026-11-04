<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PricingService
{
    protected HaversineService $haversine;

    const DEFAULT_KANTOR_LAT = -6.4239;
    const DEFAULT_KANTOR_LON = 106.9401;

    public function __construct(HaversineService $haversine)
    {
        $this->haversine = $haversine;
    }

    /**
     * Ambil konfigurasi aturan harga dari database (dengan caching)
     */
    public function getRules(): array
    {
        return Cache::remember('teksika_pricing_rules', 3600, function () {
            $records = DB::table('pricing_rules')->get();
            $rules = [];
            foreach ($records as $r) {
                $rules[$r->key] = is_numeric($r->value) ? (float) $r->value : $r->value;
            }
            return $rules;
        });
    }

    /**
     * Bersihkan cache aturan saat admin mengubah tarif
     */
    public function clearRulesCache(): void
    {
        Cache::forget('teksika_pricing_rules');
    }

    public function adjustWorkerRate(float $baseRate, float $rating, int $experienceYears): array
    {
        $ratingPremium = max(0, $rating - 3) * 0.02;
        $experiencePremium = max(0, $experienceYears) * 0.01;
        $factor = min(1.25, 1 + $ratingPremium + $experiencePremium);
        $baseRate = round(max(0, $baseRate));
        $adjustedRate = round($baseRate * $factor);

        return [
            'harga_dasar' => (float) $baseRate,
            'faktor_kualifikasi' => (float) $factor,
            'biaya_kualifikasi' => (float) ($adjustedRate - $baseRate),
            'harga_layanan' => (float) $adjustedRate,
        ];
    }

    /**
     * Hitung jarak dari Kantor Pusat TEKSIKA ke rumah user (km)
     *
     * @param float $userLat Latitude rumah user
     * @param float $userLon Longitude rumah user
     * @param float|null $kantorLat (Opsional) Override latitude kantor pusat
     * @param float|null $kantorLon (Opsional) Override longitude kantor pusat
     * @return float Jarak dalam kilometer
     */
    public function calculateDistanceToKantor(
        float $userLat,
        float $userLon,
        ?float $kantorLat = null,
        ?float $kantorLon = null
    ): float {
        $kantorLat ??= self::DEFAULT_KANTOR_LAT;
        $kantorLon ??= self::DEFAULT_KANTOR_LON;

        return $this->haversine->calculateDistance($kantorLat, $kantorLon, $userLat, $userLon, 2);
    }

    /**
     * Hitung estimasi harga dan durasi pengerjaan secara transparan.
     * Biaya transportasi dan durasi perjalanan dihitung berdasarkan JARAK KANTOR PUSAT KE RUMAH USER.
     *
     * @param float $hargaMin Harga minimum layanan
     * @param float $hargaMax Harga maksimum layanan
     * @param int $durasiDasarMenit Durasi dasar pekerjaan (menit)
     * @param string $kompleksitas Tingkat kesulitan ('rendah', 'sedang', 'tinggi')
     * @param string $urgensi Tingkat urgensi ('rendah'/'normal', 'sedang'/'mendesak', 'tinggi'/'darurat')
     * @param float $jarakKm Jarak tempuh dari kantor pusat ke rumah user (km)
     * @param float|null $userLat (Opsional) Jika diisi bersama userLon, jarak dihitung otomatis dari kantor pusat
     * @param float|null $userLon (Opsional) Jika diisi bersama userLat, jarak dihitung otomatis dari kantor pusat
     * @return array Rincian estimasi lengkap
     */
    public function calculate(
        float $hargaMin,
        float $hargaMax,
        int $durasiDasarMenit,
        string $kompleksitas = 'sedang',
        string $urgensi = 'rendah',
        float $jarakKm = 0.0,
        ?float $userLat = null,
        ?float $userLon = null,
        ?float $routeDistanceKm = null,
        ?int $routeDurationMinutes = null,
        string $tingkatKerusakan = 'ringan'
    ): array {
        $rules = $this->getRules();

        if ($routeDistanceKm !== null) {
            $jarakKm = $routeDistanceKm;
        } elseif ($userLat !== null && $userLon !== null) {
            $jarakKm = $this->calculateDistanceToKantor($userLat, $userLon);
        }

        // 1. Standarisasi Input
        $kompleksitas = strtolower(trim($kompleksitas));
        if (!in_array($kompleksitas, ['rendah', 'sedang', 'tinggi'])) {
            $kompleksitas = 'sedang';
        }

        $urgensi = strtolower(trim($urgensi));
        // Normalisasi alias normal/mendesak/darurat -> rendah/sedang/tinggi
        if ($urgensi === 'normal') $urgensi = 'rendah';
        elseif ($urgensi === 'mendesak') $urgensi = 'sedang';
        elseif ($urgensi === 'darurat') $urgensi = 'tinggi';
        if (!in_array($urgensi, ['rendah', 'sedang', 'tinggi'])) {
            $urgensi = 'rendah';
        }

        $tingkatKerusakan = strtolower(trim($tingkatKerusakan));
        if (!in_array($tingkatKerusakan, ['ringan', 'sedang', 'berat'])) {
            $tingkatKerusakan = 'ringan';
        }

        // 2. Harga Dasar Layanan berdasarkan Kompleksitas
        if ($kompleksitas === 'rendah') {
            $hargaLayanan = $hargaMin;
        } elseif ($kompleksitas === 'tinggi') {
            $hargaLayanan = $hargaMax;
        } else {
            // 'sedang' (rata-rata min dan max)
            $hargaLayanan = round(($hargaMin + $hargaMax) / 2);
        }

        // 3. Faktor Pengali Urgensi
        $faktorUrgensi = 1.00;
        if ($urgensi === 'sedang') {
            $faktorUrgensi = $rules['faktor_urgensi_sedang'] ?? 1.25;
        } elseif ($urgensi === 'tinggi') {
            $faktorUrgensi = $rules['faktor_urgensi_tinggi'] ?? 1.50;
        } else {
            $faktorUrgensi = $rules['faktor_urgensi_rendah'] ?? 1.00;
        }

        $faktorKerusakan = match ($tingkatKerusakan) {
            'sedang' => $rules['faktor_kerusakan_sedang'] ?? 1.05,
            'berat' => $rules['faktor_kerusakan_berat'] ?? 1.10,
            default => $rules['faktor_kerusakan_ringan'] ?? 1.00,
        };

        // 4. Biaya Transportasi Berdasarkan Jarak Kantor Pusat ke Rumah User (Gratis di bawah batas km)
        $jarakGratisKm = $rules['jarak_gratis_km'] ?? 5.0;
        $tarifPerKm = $rules['tarif_per_km'] ?? 2500;
        $biayaJarak = 0.0;

        if ($jarakKm > $jarakGratisKm) {
            $jarakDikenakanBiaya = $jarakKm - $jarakGratisKm;
            $biayaJarak = round($jarakDikenakanBiaya * $tarifPerKm);
        }

        // 5. Total Estimasi Biaya
        $estimasiTotal = round(($hargaLayanan * $faktorUrgensi * $faktorKerusakan) + $biayaJarak);

        // 6. Estimasi Durasi Pengerjaan & Perjalanan
        // Multiplier durasi kerja berdasarkan kompleksitas
        $pengaliDurasi = 1.00;
        if ($kompleksitas === 'sedang') {
            $pengaliDurasi = $rules['pengali_durasi_sedang'] ?? 1.50;
        } elseif ($kompleksitas === 'tinggi') {
            $pengaliDurasi = $rules['pengali_durasi_tinggi'] ?? 2.00;
        } else {
            $pengaliDurasi = $rules['pengali_durasi_rendah'] ?? 1.00;
        }

        $durasiKerjaMenit = (int) round($durasiDasarMenit * $pengaliDurasi);
        $kecepatanKmh = $rules['kecepatan_kmh'] ?? 30;
        // Waktu tempuh perjalanan dari kantor pusat ke rumah user
        $durasiTempuhMenit = $routeDurationMinutes
            ?? $this->haversine->estimateTravelTimeMinutes($jarakKm, $kecepatanKmh);
        $estimasiDurasiMenit = $durasiKerjaMenit + $durasiTempuhMenit;

        return [
            'kompleksitas' => $kompleksitas,
            'urgensi' => $urgensi,
            'tingkat_kerusakan' => $tingkatKerusakan,
            'jarak_km' => round($jarakKm, 2),
            'jarak_kantor_km' => round($jarakKm, 2),
            'harga_layanan' => (float) $hargaLayanan,
            'faktor_urgensi' => (float) $faktorUrgensi,
            'faktor_kerusakan' => (float) $faktorKerusakan,
            'biaya_jarak' => (float) $biayaJarak,
            'biaya_transportasi' => (float) $biayaJarak,
            'estimasi_total' => (float) $estimasiTotal,
            'durasi_kerja_menit' => $durasiKerjaMenit,
            'durasi_tempuh_menit' => $durasiTempuhMenit,
            'estimasi_durasi_menit' => $estimasiDurasiMenit,
            'tipe_jarak' => $routeDistanceKm !== null ? 'jarak_jalan_osrm' : 'garis_lurus_haversine',
        ];
    }
}
