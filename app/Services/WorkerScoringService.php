<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class WorkerScoringService
{
    protected HaversineService $haversine;
    protected PricingService $pricing;

    public function __construct(HaversineService $haversine, PricingService $pricing)
    {
        $this->haversine = $haversine;
        $this->pricing = $pricing;
    }

    /**
     * Dapatkan daftar tukang yang memenuhi kualifikasi beserta skor rekomendasi (Weighted Scoring)
     *
     * @param string $serviceName Nama layanan (misal 'AC', 'Listrik')
     * @param float $userLat Latitude user
     * @param float $userLon Longitude user
     * @return array List tukang dengan rincian skor & ranking, terurut dari skor tertinggi
     */
    public function rankWorkers(string $serviceName, float $userLat, float $userLon): array
    {
        $rules = $this->pricing->getRules();
        $radiusMax = (float) ($rules['radius_maksimal_km'] ?? 15.0);

        $wJarak = (float) ($rules['bobot_jarak'] ?? 0.25);
        $wRating = (float) ($rules['bobot_rating'] ?? 0.15);
        $wPengalaman = (float) ($rules['bobot_pengalaman'] ?? 0.15);
        $wBeban = (float) ($rules['bobot_beban_kerja'] ?? 0.45);

        // Ambil semua tukang aktif yang memiliki koordinat
        $workers = DB::table('workers')
            ->where('status', 'aktif')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $candidates = [];

        foreach ($workers as $worker) {
            // 1. Filter Skill: Kolom skills atau role harus memuat nama layanan
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

            // 2. Hitung Jarak Haversine
            $jarakKm = $this->haversine->calculateDistance(
                $userLat,
                $userLon,
                (float) $worker->latitude,
                (float) $worker->longitude,
                2
            );

            // Filter Radius Maksimal
            if ($jarakKm > $radiusMax) {
                continue;
            }

            // 3. Hitung Job Aktif (status 'dikonfirmasi' atau 'dikerjakan' di tabel request)
            $jobAktif = DB::table('request')
                ->where('id_worker', $worker->id)
                ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])
                ->count();

            // 4. Normalisasi Kriteria
            // a. Jarak (Cost Criterion): Semakin dekat, nilai semakin mendekati 1
            $skorJarak = max(0.0, 1.0 - ($jarakKm / $radiusMax));

            // b. Rating (Benefit Criterion): Skala 0 - 5
            $ratingVal = isset($worker->rating) ? (float) $worker->rating : 5.0;
            $skorRating = min(1.0, max(0.0, $ratingVal / 5.0));

            // c. Pengalaman (Benefit Criterion): Maksimal patokan 10 tahun
            $pengalamanTahun = isset($worker->pengalaman) ? (int) $worker->pengalaman : 0;
            $skorPengalaman = min(1.0, max(0.0, $pengalamanTahun / 10.0));

            // d. Beban Kerja (Cost Criterion): Maksimal patokan 5 job aktif
            $skorBeban = max(0.0, 1.0 - min(1.0, $jobAktif / 5.0));

            // 5. Hitung Skor Akhir (Weighted Sum)
            $skorTotal = ($wJarak * $skorJarak) +
                         ($wRating * $skorRating) +
                         ($wPengalaman * $skorPengalaman) +
                         ($wBeban * $skorBeban);

            $candidates[] = [
                'worker' => $worker,
                'id_worker' => $worker->id,
                'name' => $worker->name,
                'skills' => $worker->skills,
                'phone' => $worker->phone ?? null,
                'latitude' => (float) $worker->latitude,
                'longitude' => (float) $worker->longitude,
                'rating' => $ratingVal,
                'pengalaman' => $pengalamanTahun,
                'job_aktif' => $jobAktif,
                'jarak_km' => $jarakKm,
                'skor_jarak' => round($skorJarak, 4),
                'skor_rating' => round($skorRating, 4),
                'skor_pengalaman' => round($skorPengalaman, 4),
                'skor_beban' => round($skorBeban, 4),
                'skor_total' => round($skorTotal, 3),
            ];
        }

        // Urutkan berdasarkan skor_total DESC, jika seri urutkan jarak ASC
        usort($candidates, function ($a, $b) {
            if ($b['skor_total'] == $a['skor_total']) {
                return $a['jarak_km'] <=> $b['jarak_km'];
            }
            return $b['skor_total'] <=> $a['skor_total'];
        });

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

