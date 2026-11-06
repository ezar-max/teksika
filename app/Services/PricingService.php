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

    public function adjustWorkerRate(float $baseRate, float $rating, int $experienceYears, int $points = 0): array
    {
        $ratingPremium = max(0, $rating - 3) * 0.02;
        $rules = $this->getRules();
        $experienceRate = min(100, max(0, (float) ($rules['pengalaman_per_tahun_persen'] ?? 1)));
        $experienceCap = min(100, max(0, (float) ($rules['pengalaman_batas_persen'] ?? 25)));
        $experiencePremium = min(max(0, $experienceYears) * $experienceRate, $experienceCap) / 100;
        $baseRate = round(max(0, $baseRate));
        $ratingFee = round($baseRate * $ratingPremium);
        $experienceFee = round($baseRate * $experiencePremium);
        $points = max(0, min(100, $points));
        $pointsRate = min(100, max(0, (float) ($rules['poin_tarif_persen'] ?? 2)));
        $adjustedRate = $baseRate + $ratingFee + $experienceFee;

        return [
            'harga_dasar' => (float) $baseRate,
            'faktor_kualifikasi' => (float) ($baseRate > 0 ? $adjustedRate / $baseRate : 1),
            'biaya_kualifikasi' => (float) ($ratingFee + $experienceFee),
            'biaya_rating' => (float) $ratingFee,
            'biaya_pengalaman' => (float) $experienceFee,
            'pengalaman_persen' => (float) ($experiencePremium * 100),
            'harga_layanan' => (float) $adjustedRate,
            'poin' => $points,
            'poin_persen' => (float) $pointsRate,
            'biaya_additional' => $this->calculateAdditionalFee($baseRate, $points),
        ];
    }

    public function calculateAdditionalFee(float $baseRate, int $points): float
    {
        $baseRate = max(0, $baseRate);
        $points = max(0, min(100, $points));
        $pointsRate = min(100, max(0, (float) ($this->getRules()['poin_tarif_persen'] ?? 2)));

        return (float) round($baseRate * ($pointsRate / 100) * ($points / 100));
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
        string $tingkatKerusakan = 'ringan',
        float $biayaAdditional = 0.0,
        ?float $tarifDasarPerhitungan = null
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

        // 5. Calculate urgency and damage fees independently from the base tariff.
        $tarifDasarPerhitungan = max(0, $tarifDasarPerhitungan ?? $hargaLayanan);
        $biayaUrgensi = round($tarifDasarPerhitungan * ($faktorUrgensi - 1));
        $biayaKerusakan = round($tarifDasarPerhitungan * ($faktorKerusakan - 1));
        $biayaAdditional = max(0, $biayaAdditional);
        $estimasiTotal = round($hargaLayanan + $biayaUrgensi + $biayaKerusakan + $biayaJarak + $biayaAdditional);

        // Durasi layanan mengikuti durasi yang ditetapkan pada layanan yang dibooking.
        $durasiKerjaMenit = max(0, $durasiDasarMenit);
        // Waktu tempuh perjalanan dari kantor pusat ke rumah user
        $durasiTempuhMenit = $routeDurationMinutes
            ?? $this->haversine->estimateTravelTimeMinutes($jarakKm);

        return [
            'kompleksitas' => $kompleksitas,
            'urgensi' => $urgensi,
            'tingkat_kerusakan' => $tingkatKerusakan,
            'jarak_km' => round($jarakKm, 2),
            'jarak_kantor_km' => round($jarakKm, 2),
            'harga_layanan' => (float) $hargaLayanan,
            'faktor_urgensi' => (float) $faktorUrgensi,
            'faktor_kerusakan' => (float) $faktorKerusakan,
            'biaya_urgensi' => (float) $biayaUrgensi,
            'biaya_kerusakan' => (float) $biayaKerusakan,
            'biaya_jarak' => (float) $biayaJarak,
            'biaya_transportasi' => (float) $biayaJarak,
            'biaya_additional' => (float) $biayaAdditional,
            'estimasi_total' => (float) $estimasiTotal,
            'durasi_kerja_menit' => $durasiKerjaMenit,
            'durasi_tempuh_menit' => $durasiTempuhMenit,
            'estimasi_durasi_menit' => $durasiKerjaMenit,
            'tipe_jarak' => $routeDistanceKm !== null ? 'jarak_jalan_osrm' : 'garis_lurus_haversine',
        ];
    }
}
