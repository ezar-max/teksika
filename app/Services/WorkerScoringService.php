<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class WorkerScoringService
{
    protected HaversineService $haversine;
    protected PricingService $pricing;

    const DEFAULT_KANTOR_LAT = -6.4239;
    const DEFAULT_KANTOR_LON = 106.9401;

    public function __construct(HaversineService $haversine, PricingService $pricing)
    {
        $this->haversine = $haversine;
        $this->pricing = $pricing;
    }

    /**
     * Dapatkan tukang yang sesuai dan urutkan berdasarkan rating tertinggi.
     *
     * Konsep Jarak:
     * Dihitung dari Kantor Pusat TEKSIKA ke rumah user (bukan per lokasi tukang individual).
     * Nilai jarak ini konstan untuk seluruh kandidat tukang pada pesanan yang sama dan menjadi
     * dasar filter radius operasional layanan serta dasar penentuan biaya jarak di PricingService.
     *
     * @param string $serviceName Nama layanan (misal 'AC', 'Listrik')
     * @param float $userLat Latitude rumah user
     * @param float $userLon Longitude rumah user
     * @param float|null $kantorLat (Opsional) Override latitude kantor pusat
     * @param float|null $kantorLon (Opsional) Override longitude kantor pusat
     * @return array List tukang dengan rincian skor & ranking, terurut dari skor tertinggi
     */
    public function rankWorkers(
        string $serviceName,
        float $userLat,
        float $userLon,
        ?float $kantorLat = null,
        ?float $kantorLon = null
    ): array {
        $rules = $this->pricing->getRules();
        $radiusMax = (float) ($rules['radius_maksimal_km'] ?? 15.0);

        $kantorLat ??= self::DEFAULT_KANTOR_LAT;
        $kantorLon ??= self::DEFAULT_KANTOR_LON;

        // 1. Hitung Jarak Haversine dari Kantor Pusat TEKSIKA ke Rumah User
        $jarakKantorKm = $this->haversine->calculateDistance(
            $kantorLat,
            $kantorLon,
            $userLat,
            $userLon,
            2
        );

        // Filter Radius Maksimal Kantor Pusat ke Rumah User:
        // Jika rumah user melebihi radius operasional, kantor tidak dapat melayani pesanan ini
        if ($jarakKantorKm > $radiusMax) {
            return [];
        }

        // Jarak kantor tetap digunakan untuk batas radius operasional, bukan pemeringkatan.
        $skorJarak = max(0.0, 1.0 - ($jarakKantorKm / $radiusMax));

        $reviewStats = DB::table('worker_reviews')
            ->select('id_worker', DB::raw('COUNT(*) as review_count'), DB::raw('AVG(rating) as review_average'))
            ->groupBy('id_worker');

        $workers = DB::table('workers')
            ->leftJoinSub($reviewStats, 'review_stats', function ($join) {
                $join->on('workers.id', '=', 'review_stats.id_worker');
            })
            ->where('status', 'aktif')
            ->whereNotIn('status_ketersediaan', ['tidak_tersedia', 'libur'])
            ->select('workers.*', 'review_stats.review_count', 'review_stats.review_average')
            ->get();

        $candidates = [];

        foreach ($workers as $worker) {
            // 2. Filter Skill: Kolom skills atau role harus memuat nama layanan
            $hasSkill = false;
            $serviceNeedle = strtolower(trim($serviceName));

            if (!empty($worker->skills)) {
                $skillsList = array_map('trim', explode(',', strtolower($worker->skills)));
                foreach ($skillsList as $s) {
                    if (str_contains($s, $serviceNeedle) || str_contains($serviceNeedle, $s)) {
                        $hasSkill = true;
                        break;
                    }
                }
            }

            if (!$hasSkill && !empty($worker->role)) {
                if (str_contains(strtolower($worker->role), $serviceNeedle)) {
                    $hasSkill = true;
                }
            }

            if (!$hasSkill) {
                continue; // Skip jika tidak memiliki skill yang sesuai
            }

            // 3. Hitung Job Aktif (status 'dikonfirmasi' atau 'dikerjakan' di tabel request)
            $jobAktif = DB::table('request')
                ->where('id_worker', $worker->id)
                ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])
                ->count();

            if ($jobAktif > 0 || ($worker->status_ketersediaan ?? 'siap') === 'sedang_bekerja') {
                continue;
            }

            // 4. Normalisasi Kriteria Penilaian Tukang
            // a. Rating (Benefit Criterion): Skala 0 - 5
            $ratingVal = $worker->review_count > 0
                ? (float) $worker->review_average
                : (isset($worker->rating) ? (float) $worker->rating : 5.0);
            $skorRating = min(1.0, max(0.0, $ratingVal / 5.0));

            // b. Pengalaman (Benefit Criterion): Maksimal patokan 10 tahun
            $pengalamanTahun = isset($worker->pengalaman) ? (int) $worker->pengalaman : 0;
            $skorPengalaman = min(1.0, max(0.0, $pengalamanTahun / 10.0));

            // c. Beban Kerja (Cost Criterion): Maksimal patokan 5 job aktif (makin sedikit makin tinggi nilainya)
            $skorBeban = max(0.0, 1.0 - min(1.0, $jobAktif / 5.0));

            $candidates[] = [
                'worker' => $worker,
                'id_worker' => $worker->id,
                'name' => $worker->name,
                'skills' => $worker->skills,
                'tarif' => $worker->tarif,
                'foto' => $worker->foto ?? null,
                'phone' => $worker->phone ?? null,
                'latitude' => (float) ($worker->latitude ?? $kantorLat),
                'longitude' => (float) ($worker->longitude ?? $kantorLon),
                'rating' => $ratingVal,
                'poin' => max(0, min(100, (int) ($worker->poin ?? 0))),
                'jumlah_ulasan' => (int) ($worker->review_count ?? 0),
                'pengalaman' => $pengalamanTahun,
                'job_aktif' => $jobAktif,
                'jarak_km' => $jarakKantorKm,
                'skor_jarak' => round($skorJarak, 4),
                'skor_rating' => round($skorRating, 4),
                'skor_pengalaman' => round($skorPengalaman, 4),
                'skor_beban' => round($skorBeban, 4),
                'skor_total' => round($skorRating, 3),
            ];
        }

        usort($candidates, fn ($a, $b) => ($b['rating'] <=> $a['rating']) ?: ($a['id_worker'] <=> $b['id_worker']));

        // Beri ranking 1, 2, 3...
        $ranked = [];
        $rank = 1;
        foreach ($candidates as $c) {
            $c['ranking'] = $rank++;
            $ranked[] = $c;
        }

        return $ranked;
    }

    /**
     * Dapatkan titik koordinat kantor pusat aktif
     *
     * @return array ['latitude' => float, 'longitude' => float]
     */
    public function getKantorCoordinates(): array
    {
        $rules = $this->pricing->getRules();
        return [
            'latitude' => self::DEFAULT_KANTOR_LAT,
            'longitude' => self::DEFAULT_KANTOR_LON,
            'name' => 'Kantor Pusat TEKSIKA - SMKN 1 Gunung Putri',
        ];
    }

    /**
     * Simpan hasil ranking maksimal 3 tukang ke tabel rekomendasi_tukang
     *
     * @param int $idRequest ID request dari tabel request
     * @param array $rankedWorkers Array hasil rankWorkers()
     */
    public function saveRecommendations(int $idRequest, array $rankedWorkers): void
    {
        // Hapus rekomendasi lama jika ada (untuk re-calculate)
        DB::table('rekomendasi_tukang')->where('id_request', $idRequest)->delete();

        // Ambil maksimal 3 teratas
        $top3 = array_slice($rankedWorkers, 0, 3);

        foreach ($top3 as $r) {
            DB::table('rekomendasi_tukang')->insert([
                'id_request' => $idRequest,
                'id_worker' => $r['id_worker'],
                'jarak_km' => $r['jarak_km'],
                'skor_jarak' => $r['skor_jarak'],
                'skor_rating' => $r['skor_rating'],
                'skor_pengalaman' => $r['skor_pengalaman'],
                'skor_beban' => $r['skor_beban'],
                'skor_total' => $r['skor_total'],
                'ranking' => $r['ranking'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
