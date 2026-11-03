<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\HaversineService;
use App\Services\PricingService;
use App\Services\WorkerScoringService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlgoritmaTest extends TestCase
{
    use DatabaseTransactions;

    protected HaversineService $haversine;
    protected PricingService $pricing;
    protected WorkerScoringService $scoring;

    protected function setUp(): void
    {
        parent::setUp();
        $this->haversine = app(HaversineService::class);
        $this->pricing = app(PricingService::class);
        $this->scoring = app(WorkerScoringService::class);
    }

    /**
     * Uji Algoritma 1: Haversine Jarak & Durasi Tempuh
     */
    public function test_haversine_distance_and_travel_time(): void
    {
        $userLat = -6.2000;
        $userLon = 106.8000;

        // Tukang A: -6.1730, 106.8000 (sekitar 3.0 km)
        $distA = $this->haversine->calculateDistance($userLat, $userLon, -6.1730, 106.8000, 1);
        $this->assertEquals(3.0, $distA, 'Jarak Tukang A harus sekitar 3.0 km');

        // Tukang B: -6.1191, 106.8000 (sekitar 9.0 km)
        $distB = $this->haversine->calculateDistance($userLat, $userLon, -6.1191, 106.8000, 1);
        $this->assertEquals(9.0, $distB, 'Jarak Tukang B harus sekitar 9.0 km');

        // Estimasi waktu tempuh untuk 3.0 km (kecepatan 30 km/jam -> 2 menit/km -> 6 menit)
        $travelTime = $this->haversine->estimateTravelTimeMinutes(3.0, 30);
        $this->assertEquals(6, $travelTime, 'Waktu tempuh 3 km harus 6 menit');
    }

    /**
     * Uji Algoritma 2: Rule-based Pricing & Durasi
     * Skenario: AC (100.000 - 200.000), Durasi 60 menit, Kompleksitas Sedang, Urgensi Mendesak, Jarak 3.0 km
     */
    public function test_rule_based_pricing_calculation(): void
    {
        $res = $this->pricing->calculate(
            hargaMin: 100000,
            hargaMax: 200000,
            durasiDasarMenit: 60,
            kompleksitas: 'sedang',
            urgensi: 'mendesak',
            jarakKm: 3.0
        );

        $this->assertEquals(150000, $res['harga_layanan'], 'Harga layanan kompleksitas sedang harus 150.000');
        $this->assertEquals(1.25, $res['faktor_urgensi'], 'Faktor urgensi mendesak harus 1.25');
        $this->assertEquals(0, $res['biaya_jarak'], 'Biaya jarak di bawah 5 km harus Rp0 (gratis)');
        $this->assertEquals(187500, $res['estimasi_total'], 'Total estimasi harus 187.500');
        $this->assertEquals(90, $res['durasi_kerja_menit'], 'Durasi kerja sedang harus 90 menit (60 x 1.5)');
        $this->assertEquals(6, $res['durasi_tempuh_menit'], 'Durasi tempuh harus 6 menit');
        $this->assertEquals(96, $res['estimasi_durasi_menit'], 'Total durasi harus 96 menit');
    }

    /**
     * Uji Algoritma 3: Weighted Scoring & Ranking Tukang
     */
    public function test_worker_scoring_and_ranking(): void
    {
        // 1. Bersihkan tukang aktif testing terdahulu jika ada
        DB::table('workers')->whereIn('name', ['Tukang A (Test)', 'Tukang B (Test)', 'Tukang Jauh (Test)'])->delete();

        // 2. Siapkan data Tukang A
        $idA = DB::table('workers')->insertGetId([
            'name' => 'Tukang A (Test)',
            'role' => 'Teknisi AC',
            'alamat' => 'Jakarta Pusat',
            'phone' => '081234567891',
            'skills' => 'AC, Elektronik',
            'pengalaman' => 5,
            'rating' => 4.80,
            'latitude' => -6.1730,
            'longitude' => 106.8000,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Siapkan data Tukang B
        $idB = DB::table('workers')->insertGetId([
            'name' => 'Tukang B (Test)',
            'role' => 'Teknisi AC',
            'alamat' => 'Jakarta Utara',
            'phone' => '081234567892',
            'skills' => 'AC, Pendingin',
            'pengalaman' => 8,
            'rating' => 4.50,
            'latitude' => -6.1191,
            'longitude' => 106.8000,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Tukang C di luar radius 15 km (harus diabaikan)
        $idC = DB::table('workers')->insertGetId([
            'name' => 'Tukang Jauh (Test)',
            'role' => 'Teknisi AC',
            'alamat' => 'Bogor',
            'phone' => '081234567893',
            'skills' => 'AC',
            'pengalaman' => 10,
            'rating' => 5.00,
            'latitude' => -6.5971, // > 40 km dari Jakarta
            'longitude' => 106.8060,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Jalankan ranking untuk lokasi user: -6.2000, 106.8000
        $ranked = $this->scoring->rankWorkers('AC', -6.2000, 106.8000);

        // Filter kandidat test kita
        $filtered = array_values(array_filter($ranked, function ($item) use ($idA, $idB) {
            return in_array($item['id_worker'], [$idA, $idB]);
        }));

        $this->assertCount(2, $filtered, 'Harus ada 2 kandidat tukang dalam radius');

        // Tukang A harus di Peringkat 1
        $this->assertEquals($idA, $filtered[0]['id_worker'], 'Tukang A harus di peringkat 1');
        $this->assertEqualsWithDelta(0.869, $filtered[0]['skor_total'], 0.015, 'Skor Tukang A harus sekitar 0.869');

        // Tukang B harus di Peringkat 2
        $this->assertEquals($idB, $filtered[1]['id_worker'], 'Tukang B harus di peringkat 2');
        $this->assertEqualsWithDelta(0.805, $filtered[1]['skor_total'], 0.015, 'Skor Tukang B harus sekitar 0.805');

        // Tukang Jauh (idC) tidak boleh masuk ke ranking
        $ids = array_column($ranked, 'id_worker');
        $this->assertNotContains($idC, $ids, 'Tukang di luar radius 15 km tidak boleh masuk rekomendasi');
    }
}
