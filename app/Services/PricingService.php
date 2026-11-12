<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PricingService
{
    protected HaversineService $haversine;

    const DEFAULT_KANTOR_LAT = -6.4239;
    const DEFAULT_KANTOR_LON = 106.9401;

    private const ALLOWED_ROUNDING_STEPS = [1, 100, 500, 1000];

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
     * Normalisasi dan validasi parameter harga agar tidak bernilai negatif atau melebihi batas wajar.
     */
    public function getNormalizedRules(): array
    {
        $raw = $this->getRules();

        $num = static function (string $key, float $default, float $min = 0.0, ?float $max = null) use ($raw): float {
            $val = isset($raw[$key]) && is_numeric($raw[$key]) ? (float) $raw[$key] : $default;
            if (!is_finite($val)) {
                $val = $default;
            }
            $val = max($min, $val);
            if ($max !== null) {
                $val = min($max, $val);
            }
            return $val;
        };

        $persenKualifikasiMin = $num('persen_kualifikasi_min', 0.0, 0.0, 100.0);
        $persenKualifikasiMax = $num('persen_kualifikasi_maks', 35.0, $persenKualifikasiMin, 100.0);

        $batasMin = (int) round($num('batas_harga_jasa_min', 0.0, 0.0));
        $batasMax = (int) round($num('batas_harga_jasa_max', 0.0, 0.0));
        if ($batasMax > 0 && $batasMax < $batasMin) {
            $batasMax = $batasMin;
        }

        $roundingStep = (int) round($num('pembulatan_harga', 100.0, 1.0, 1000.0));
        if (!in_array($roundingStep, self::ALLOWED_ROUNDING_STEPS, true)) {
            $roundingStep = 100;
        }

        $modeTransportasi = strtolower(trim((string) ($raw['mode_transportasi'] ?? 'satu_arah')));
        if (!in_array($modeTransportasi, ['satu_arah', 'pulang_pergi'], true)) {
            $modeTransportasi = 'satu_arah';
        }

        return [
            'jarak_gratis_km' => $num('jarak_gratis_km', 5.0, 0.0, 100.0),
            'tarif_per_km' => $num('tarif_per_km', 2500.0, 0.0),
            'radius_maksimal_km' => $num('radius_maksimal_km', 15.0, 1.0, 100.0),
            'mode_transportasi' => $modeTransportasi,
            'faktor_urgensi_rendah' => $num('faktor_urgensi_rendah', 1.00, 1.00, 3.00),
            'faktor_urgensi_sedang' => $num('faktor_urgensi_sedang', 1.20, 1.00, 3.00),
            'faktor_urgensi_tinggi' => $num('faktor_urgensi_tinggi', 1.40, 1.00, 3.00),
            'faktor_kerusakan_ringan' => $num('faktor_kerusakan_ringan', 1.00, 1.00, 3.00),
            'faktor_kerusakan_sedang' => $num('faktor_kerusakan_sedang', 1.05, 1.00, 3.00),
            'faktor_kerusakan_berat' => $num('faktor_kerusakan_berat', 1.10, 1.00, 3.00),
            'persen_rating_maks' => $num('persen_rating_maks', 15.0, 0.0, 100.0),
            'persen_pengalaman_per_tahun' => $num('persen_pengalaman_per_tahun', 2.0, 0.0, 50.0),
            'persen_pengalaman_maks' => $num('persen_pengalaman_maks', 20.0, 0.0, 100.0),
            'persen_kualifikasi_min' => $persenKualifikasiMin,
            'persen_kualifikasi_maks' => $persenKualifikasiMax,
            'batas_harga_jasa_min' => $batasMin,
            'batas_harga_jasa_max' => $batasMax,
            'pembulatan_harga' => $roundingStep,
        ];
    }

    /**
     * Bersihkan cache aturan saat admin mengubah tarif
     */
    public function clearRulesCache(): void
    {
        Cache::forget('teksika_pricing_rules');
    }

    /**
     * Pembulatan nominal rupiah secara konsisten ke kelipatan yang dikonfigurasi admin.
     */
    public function roundMoney(float $amount, ?int $step = null): int
    {
        if (!is_finite($amount) || $amount <= 0) {
            return 0;
        }

        $step ??= $this->getNormalizedRules()['pembulatan_harga'];
        if ($step <= 1) {
            return (int) round($amount);
        }

        return (int) (round($amount / $step) * $step);
    }

    /**
     * Hitung biaya transportasi berdasarkan jarak, batas gratis, mode perjalanan, dan pembulatan.
     */
    public function calculateTransportFee(float $distanceKm): int
    {
        $rules = $this->getNormalizedRules();
        $safeDistance = is_finite($distanceKm) ? max(0.0, $distanceKm) : 0.0;
        $multiplier = $rules['mode_transportasi'] === 'pulang_pergi' ? 2.0 : 1.0;
        $billableDistance = max(0.0, $safeDistance - $rules['jarak_gratis_km']) * $multiplier;

        return $this->roundMoney($billableDistance * $rules['tarif_per_km'], $rules['pembulatan_harga']);
    }

    /**
     * Hitung penyesuaian rating & pengalaman tukang secara proporsional dan rasional.
     *
     * Model perhitungan:
     * - Basis tarif layanan: $baseRate (harga_min layanan, >= 0)
     * - Basis penyesuaian tukang: $workerCustomRate jika diset (> 0), jika tidak memakai $baseRate
     * - Persentase rating: (rating / 5.0) * persen_rating_maks
     * - Persentase pengalaman: min(tahun * persen_pengalaman_per_tahun, persen_pengalaman_maks)
     * - Gabungan persentase di-clamp antara [persen_kualifikasi_min, persen_kualifikasi_maks]
     * - Biaya jasa tukang = Tarif dasar layanan + Penyesuaian rating + Penyesuaian pengalaman
     */
    public function adjustWorkerRate(
        float $baseRate,
        ?float $rating,
        int $experienceYears,
        ?float $workerCustomRate = null
    ): array {
        $rules = $this->getNormalizedRules();
        $serviceBase = max(0, (int) round(is_finite($baseRate) ? $baseRate : 0));
        $qualificationBase = ($workerCustomRate !== null && is_finite($workerCustomRate) && $workerCustomRate > 0)
            ? (int) round($workerCustomRate)
            : $serviceBase;

        if ($rating === null) {
            return [
                'harga_dasar' => (float) $serviceBase,
                'basis_tukang' => (float) $qualificationBase,
                'faktor_kualifikasi' => 1.0,
                'biaya_kualifikasi' => 0.0,
                'biaya_rating' => 0.0,
                'biaya_pengalaman' => 0.0,
                'persen_rating' => 0.0,
                'persen_pengalaman' => 0.0,
                'persen_kualifikasi' => 0.0,
                'skor_rating' => null,
                'skor_pengalaman' => 0.0,
                'skor_total' => null,
                'pengalaman_persen' => 0.0,
                'harga_layanan' => (float) $serviceBase,
                'biaya_additional' => 0.0,
            ];
        }

        $clampedRating = min(5.0, max(0.0, is_finite($rating) ? $rating : 0.0));
        $clampedExp = max(0, $experienceYears);

        $ratingPct = ($clampedRating / 5.0) * $rules['persen_rating_maks'];
        $expPct = min($clampedExp * $rules['persen_pengalaman_per_tahun'], $rules['persen_pengalaman_maks']);
        $rawTotalPct = $ratingPct + $expPct;

        $cappedTotalPct = min($rules['persen_kualifikasi_maks'], max($rules['persen_kualifikasi_min'], $rawTotalPct));
        if ($rawTotalPct > 0 && $rawTotalPct > $rules['persen_kualifikasi_maks']) {
            $scale = $rules['persen_kualifikasi_maks'] / $rawTotalPct;
            $ratingPct *= $scale;
            $expPct *= $scale;
        }

        $step = $rules['pembulatan_harga'];
        $ratingFee = $this->roundMoney($qualificationBase * ($ratingPct / 100), $step);
        $experienceFee = $this->roundMoney($qualificationBase * ($expPct / 100), $step);
        $qualificationFee = $ratingFee + $experienceFee;
        $workerServiceTotal = $serviceBase + $qualificationFee;

        $faktorKualifikasi = $serviceBase > 0
            ? round($workerServiceTotal / $serviceBase, 4)
            : 1.0;

        return [
            'harga_dasar' => (float) $serviceBase,
            'basis_tukang' => (float) $qualificationBase,
            'faktor_kualifikasi' => (float) $faktorKualifikasi,
            'biaya_kualifikasi' => (float) $qualificationFee,
            'biaya_rating' => (float) $ratingFee,
            'biaya_pengalaman' => (float) $experienceFee,
            'persen_rating' => round($ratingPct, 2),
            'persen_pengalaman' => round($expPct, 2),
            'persen_kualifikasi' => round($cappedTotalPct, 2),
            'skor_rating' => round($ratingPct, 2),
            'skor_pengalaman' => round($expPct, 2),
            'skor_total' => round($cappedTotalPct, 2),
            'pengalaman_persen' => round($expPct, 2),
            'harga_layanan' => (float) $workerServiceTotal,
            'biaya_additional' => 0.0,
        ];
    }

    public function serviceRateForComplexity(float $hargaMin, float $hargaMax, string $kompleksitas): float
    {
        $min = max(0.0, is_finite($hargaMin) ? $hargaMin : 0.0);
        $max = max($min, is_finite($hargaMax) ? $hargaMax : $min);

        return match (strtolower(trim($kompleksitas))) {
            'rendah' => $min,
            'tinggi' => $max,
            default => round(($min + $max) / 2),
        };
    }

    /**
     * Hitung jarak dari Kantor Pusat TEKSIKA ke rumah user (km)
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
     * Hitung estimasi harga dan durasi pengerjaan secara transparan dan konsisten.
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
        ?float $tarifDasarPerhitungan = null,
        float $biayaMaterial = 0.0
    ): array {
        $rules = $this->getNormalizedRules();
        $step = $rules['pembulatan_harga'];

        if ($routeDistanceKm !== null && is_finite($routeDistanceKm) && $routeDistanceKm >= 0) {
            $jarakKm = $routeDistanceKm;
        } elseif ($userLat !== null && $userLon !== null) {
            $jarakKm = $this->calculateDistanceToKantor($userLat, $userLon);
        }
        $jarakKm = is_finite($jarakKm) ? max(0.0, $jarakKm) : 0.0;

        $kompleksitas = strtolower(trim($kompleksitas));
        if (!in_array($kompleksitas, ['rendah', 'sedang', 'tinggi'], true)) {
            $kompleksitas = 'sedang';
        }

        $urgensi = strtolower(trim($urgensi));
        if ($urgensi === 'normal') {
            $urgensi = 'rendah';
        } elseif ($urgensi === 'mendesak') {
            $urgensi = 'sedang';
        } elseif ($urgensi === 'darurat') {
            $urgensi = 'tinggi';
        }
        if (!in_array($urgensi, ['rendah', 'sedang', 'tinggi'], true)) {
            $urgensi = 'rendah';
        }

        $tingkatKerusakan = strtolower(trim($tingkatKerusakan));
        if (!in_array($tingkatKerusakan, ['ringan', 'sedang', 'berat'], true)) {
            $tingkatKerusakan = 'ringan';
        }

        $biayaJasaTukang = $this->roundMoney($this->serviceRateForComplexity($hargaMin, $hargaMax, $kompleksitas), $step);
        $tarifDasarLayanan = $this->roundMoney($tarifDasarPerhitungan ?? $biayaJasaTukang, $step);

        $faktorUrgensi = match ($urgensi) {
            'sedang' => $rules['faktor_urgensi_sedang'],
            'tinggi' => $rules['faktor_urgensi_tinggi'],
            default => $rules['faktor_urgensi_rendah'],
        };

        $faktorKerusakan = match ($tingkatKerusakan) {
            'sedang' => $rules['faktor_kerusakan_sedang'],
            'berat' => $rules['faktor_kerusakan_berat'],
            default => $rules['faktor_kerusakan_ringan'],
        };

        $biayaUrgensi = $this->roundMoney($tarifDasarLayanan * max(0.0, $faktorUrgensi - 1), $step);
        $biayaKerusakan = $this->roundMoney($tarifDasarLayanan * max(0.0, $faktorKerusakan - 1), $step);

        $subtotalJasa = $biayaJasaTukang + $biayaKerusakan + $biayaUrgensi;
        if ($rules['batas_harga_jasa_min'] > 0) {
            $subtotalJasa = max($rules['batas_harga_jasa_min'], $subtotalJasa);
        }
        if ($rules['batas_harga_jasa_max'] > 0) {
            $subtotalJasa = min($rules['batas_harga_jasa_max'], $subtotalJasa);
        }
        $subtotalJasa = $this->roundMoney($subtotalJasa, $step);

        $jarakGratisKm = $rules['jarak_gratis_km'];
        $tarifPerKm = $rules['tarif_per_km'];
        $multiplierJarak = $rules['mode_transportasi'] === 'pulang_pergi' ? 2.0 : 1.0;
        $jarakDitagihKm = max(0.0, $jarakKm - $jarakGratisKm) * $multiplierJarak;
        $biayaJarak = $this->roundMoney($jarakDitagihKm * $tarifPerKm, $step);

        $biayaAdditional = $this->roundMoney(max(0.0, is_finite($biayaAdditional) ? $biayaAdditional : 0.0), $step);
        $biayaMaterial = $this->roundMoney(max(0.0, is_finite($biayaMaterial) ? $biayaMaterial : 0.0), $step);

        $estimasiTotal = $this->roundMoney($subtotalJasa + $biayaJarak + $biayaMaterial + $biayaAdditional, $step);

        $durasiKerjaMenit = max(0, $durasiDasarMenit);
        $durasiTempuhMenit = $routeDurationMinutes
            ?? $this->haversine->estimateTravelTimeMinutes($jarakKm);

        return [
            'kompleksitas' => $kompleksitas,
            'urgensi' => $urgensi,
            'tingkat_kerusakan' => $tingkatKerusakan,
            'jarak_km' => round($jarakKm, 2),
            'jarak_kantor_km' => round($jarakKm, 2),
            'jarak_gratis_km' => round($jarakGratisKm, 2),
            'jarak_ditagih_km' => round($jarakDitagihKm, 2),
            'tarif_per_km' => (float) $tarifPerKm,
            'mode_transportasi' => $rules['mode_transportasi'],
            'tarif_dasar_layanan' => (float) $tarifDasarLayanan,
            'harga_layanan' => (float) $biayaJasaTukang,
            'subtotal_jasa' => (float) $subtotalJasa,
            'faktor_urgensi' => (float) $faktorUrgensi,
            'faktor_kerusakan' => (float) $faktorKerusakan,
            'persen_urgensi' => round(max(0.0, $faktorUrgensi - 1) * 100, 1),
            'persen_kerusakan' => round(max(0.0, $faktorKerusakan - 1) * 100, 1),
            'biaya_urgensi' => (float) $biayaUrgensi,
            'biaya_kerusakan' => (float) $biayaKerusakan,
            'biaya_jarak' => (float) $biayaJarak,
            'biaya_transportasi' => (float) $biayaJarak,
            'biaya_material' => (float) $biayaMaterial,
            'biaya_additional' => (float) $biayaAdditional,
            'pembulatan_harga' => $step,
            'estimasi_total' => (float) $estimasiTotal,
            'durasi_kerja_menit' => $durasiKerjaMenit,
            'durasi_tempuh_menit' => $durasiTempuhMenit,
            'estimasi_durasi_menit' => $durasiKerjaMenit,
            'tipe_jarak' => $routeDistanceKm !== null ? 'jarak_jalan_osrm' : 'garis_lurus_haversine',
        ];
    }

    /**
     * Satu pintu perhitungan estimasi lengkap dari data layanan, tukang, kondisi pekerjaan, dan rute.
     * Dipakai oleh seluruh controller & endpoint AJAX agar tidak ada duplikasi rumus.
     */
    public function calculateFullEstimate(
        float $serviceBaseRate,
        ?float $workerRating,
        int $workerExperienceYears,
        ?float $workerCustomRate = null,
        int $durasiDasarMenit = 60,
        string $kompleksitas = 'sedang',
        string $urgensi = 'rendah',
        string $tingkatKerusakan = 'ringan',
        ?float $routeDistanceKm = null,
        ?int $routeDurationMinutes = null,
        float $biayaMaterial = 0.0,
        float $biayaAdditional = 0.0
    ): array {
        $workerRate = $this->adjustWorkerRate(
            $serviceBaseRate,
            $workerRating,
            $workerExperienceYears,
            $workerCustomRate
        );

        $pricing = $this->calculate(
            hargaMin: $workerRate['harga_layanan'],
            hargaMax: $workerRate['harga_layanan'],
            durasiDasarMenit: $durasiDasarMenit,
            kompleksitas: $kompleksitas,
            urgensi: $urgensi,
            jarakKm: $routeDistanceKm ?? 0.0,
            routeDistanceKm: $routeDistanceKm,
            routeDurationMinutes: $routeDurationMinutes,
            tingkatKerusakan: $tingkatKerusakan,
            biayaAdditional: $biayaAdditional,
            tarifDasarPerhitungan: $workerRate['harga_dasar'],
            biayaMaterial: $biayaMaterial
        );

        return [
            'worker_rate' => $workerRate,
            'pricing' => $pricing,
        ];
    }
}
