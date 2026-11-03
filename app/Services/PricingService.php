<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PricingService
{
    protected HaversineService $haversine;

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

    /**
     * Hitung estimasi harga dan durasi pengerjaan secara transparan.
     *
     * @param float $hargaMin Harga minimum layanan
     * @param float $hargaMax Harga maksimum layanan
     * @param int $durasiDasarMenit Durasi dasar pekerjaan (menit)
     * @param string $kompleksitas Tingkat kesulitan ('rendah', 'sedang', 'tinggi')
     * @param string $urgensi Tingkat urgensi ('rendah'/'normal', 'sedang'/'mendesak', 'tinggi'/'darurat')
     * @param float $jarakKm Jarak tempuh dari tukang terdekat (km)
     * @return array Rincian estimasi lengkap
     */
    public function calculate(
        float $hargaMin,
        float $hargaMax,
        int $durasiDasarMenit,
        string $kompleksitas = 'sedang',
        string $urgensi = 'rendah',
        float $jarakKm = 0.0
    ): array {
        $rules = $this->getRules();

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

        // 4. Biaya Jarak (Gratis di bawah batas km)
        $jarakGratisKm = $rules['jarak_gratis_km'] ?? 5.0;
        $tarifPerKm = $rules['tarif_per_km'] ?? 2500;
        $biayaJarak = 0.0;

        if ($jarakKm > $jarakGratisKm) {
            $jarakDikenakanBiaya = $jarakKm - $jarakGratisKm;
            $biayaJarak = round($jarakDikenakanBiaya * $tarifPerKm);
        }

        // 5. Total Estimasi Harga
        $estimasiTotal = round(($hargaLayanan * $faktorUrgensi) + $biayaJarak);

        // 6. Estimasi Durasi
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
        $durasiTempuhMenit = $this->haversine->estimateTravelTimeMinutes($jarakKm, $kecepatanKmh);
        $estimasiDurasiMenit = $durasiKerjaMenit + $durasiTempuhMenit;

        return [
            'kompleksitas' => $kompleksitas,
            'urgensi' => $urgensi,
            'jarak_km' => round($jarakKm, 2),
            'harga_layanan' => (float) $hargaLayanan,
            'faktor_urgensi' => (float) $faktorUrgensi,
            'biaya_jarak' => (float) $biayaJarak,
            'estimasi_total' => (float) $estimasiTotal,
            'durasi_kerja_menit' => $durasiKerjaMenit,
            'durasi_tempuh_menit' => $durasiTempuhMenit,
            'estimasi_durasi_menit' => $estimasiDurasiMenit,
        ];
    }
}

