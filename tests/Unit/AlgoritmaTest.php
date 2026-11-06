<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\HaversineService;
use App\Services\OpenStreetMapGeocodingService;
use App\Services\PricingService;
use App\Services\WorkerScoringService;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

        // Pastikan parameter algoritma uji menggunakan nilai default standar
<<<<<<< HEAD
        DB::table('pricing_rules')->updateOrInsert(['key' => 'tarif_per_km'], ['value' => '2500']);
        DB::table('pricing_rules')->updateOrInsert(['key' => 'radius_maksimal_km'], ['value' => '15.0']);
        DB::table('pricing_rules')->updateOrInsert(['key' => 'jarak_gratis_km'], ['value' => '5.0']);
        DB::table('pricing_rules')->updateOrInsert(['key' => 'bobot_rating'], ['value' => '0.50']);
        DB::table('pricing_rules')->updateOrInsert(['key' => 'bobot_pengalaman'], ['value' => '0.50']);
        DB::table('pricing_rules')->whereIn('key', ['bobot_jarak', 'bobot_beban_kerja', 'kecepatan_kmh'])->delete();
=======
        $this->setPricingRule('tarif_per_km', '2500');
        $this->setPricingRule('radius_maksimal_km', '15.0');
        $this->setPricingRule('jarak_gratis_km', '5.0');
        $this->setPricingRule('poin_tarif_persen', '2.00');
        $this->setPricingRule('pengalaman_per_tahun_persen', '1.00');
        $this->setPricingRule('pengalaman_batas_persen', '25.00');
>>>>>>> 40abfce1492bc7d6974163b6b4b29023d1a61d3b
        $this->pricing->clearRulesCache();
    }

    private function setPricingRule(string $key, string $value): void
    {
        DB::table('pricing_rules')->updateOrInsert(
            ['key' => $key],
            [
                'value' => $value,
                'label' => $key,
                'tipe' => 'number',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
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

        Cache::flush();
        Http::fake([
            'router.project-osrm.org/*' => Http::response([
                'code' => 'Ok',
                'routes' => [[
                    'distance' => 4200,
                    'duration' => 600,
                    'geometry' => [
                        'coordinates' => [[106.8000, -6.2000], [106.8000, -6.1730]],
                    ],
                ]],
            ]),
        ]);

        $route = $this->haversine->getDrivingRoute($userLat, $userLon, -6.1730, 106.8000);
        $this->assertSame(4.2, $route['distance_km']);
        $this->assertSame(10, $route['duration_minutes']);
        $this->assertSame([[106.8000, -6.2000], [106.8000, -6.1730]], $route['geometry']);
        $this->assertSame('osrm', $route['source']);
        Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'router.project-osrm.org/route/v1/driving/'));
    }

    public function test_worker_rate_adjustment_uses_rating_and_experience_with_a_cap(): void
    {
        $pricing = new PricingService($this->haversine);

        $adjusted = $pricing->adjustWorkerRate(100000, 4.5, 4);
        $this->assertSame(100000.0, $adjusted['harga_dasar']);
        $this->assertSame(1.07, $adjusted['faktor_kualifikasi']);
        $this->assertSame(7000.0, $adjusted['biaya_kualifikasi']);
        $this->assertSame(107000.0, $adjusted['harga_layanan']);

        $capped = $pricing->adjustWorkerRate(100000, 5, 30);
        $this->assertSame(1.29, $capped['faktor_kualifikasi']);
        $this->assertSame(4000.0, $capped['biaya_rating']);
        $this->assertSame(25000.0, $capped['biaya_pengalaman']);
        $this->assertSame(29000.0, $capped['biaya_kualifikasi']);
    }

    public function test_additional_fee_uses_worker_points_as_a_capped_percentage(): void
    {
        $this->assertSame(800.0, $this->pricing->calculateAdditionalFee(200000, 20));
        $this->assertSame(2000.0, $this->pricing->calculateAdditionalFee(200000, 50));
        $this->assertSame(4000.0, $this->pricing->calculateAdditionalFee(200000, 100));
        $this->assertSame(4000.0, $this->pricing->calculateAdditionalFee(200000, 150));
        $this->assertSame(0.0, $this->pricing->calculateAdditionalFee(200000, -10));

        $pricing = $this->pricing->calculate(
            hargaMin: 200000,
            hargaMax: 200000,
            durasiDasarMenit: 60,
            kompleksitas: 'rendah',
            urgensi: 'rendah',
            biayaAdditional: $this->pricing->calculateAdditionalFee(200000, 100)
        );

        $this->assertSame(4000.0, $pricing['biaya_additional']);
        $this->assertSame(204000.0, $pricing['estimasi_total']);
    }

    public function test_worker_experience_price_rules_are_configurable(): void
    {
        $this->setPricingRule('pengalaman_per_tahun_persen', '2.00');
        $this->setPricingRule('pengalaman_batas_persen', '10.00');
        $this->pricing->clearRulesCache();

        $adjusted = $this->pricing->adjustWorkerRate(100000, 3, 8);

        $this->assertSame(10.0, $adjusted['pengalaman_persen']);
        $this->assertSame(10000.0, $adjusted['biaya_pengalaman']);
        $this->assertSame(0.0, $adjusted['biaya_rating']);
    }

    public function test_google_compute_routes_distance_and_polyline_decoding(): void
    {
        Cache::flush();
        config(['services.google_maps.api_key' => 'test-demo-key']);

        Http::fake([
            'routes.googleapis.com/directions/v2:computeRoutes*' => Http::response([
                'routes' => [[
                    'distanceMeters' => 2393,
                    'duration' => '578s',
                    'polyline' => [
                        'encodedPolyline' => '_p~iF~ps|U_ulLnnqC_mqNvxq`@',
                    ],
                ]],
            ], 200),
        ]);

        $route = $this->haversine->getDrivingRoute(-6.4239, 106.9401, -6.4300, 106.9500);

        $this->assertNotNull($route);
        $this->assertSame(2.39, $route['distance_km']);
        $this->assertSame(10, $route['duration_minutes']);
        $this->assertSame('google_routes', $route['source']);
        $this->assertNotEmpty($route['geometry']);
        Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'routes.googleapis.com/directions/v2:computeRoutes'));
    }

    public function test_osrm_failure_returns_no_route(): void
    {
        Cache::flush();
        Http::fake([
            'router.project-osrm.org/*' => Http::response(['code' => 'NoRoute'], 200),
        ]);

        $route = $this->haversine->getDrivingRoute(-6.4239, 106.9401, -6.4200, 106.9500);

        $this->assertNull($route, 'Kegagalan OSRM tidak boleh diubah menjadi jarak jalan perkiraan.');
    }

    public function test_reverse_geocoder_maps_openstreetmap_address_parts_and_caches_result(): void
    {
        Cache::flush();
        Http::fake([
            'nominatim.openstreetmap.org/reverse*' => Http::response([
                'display_name' => 'Jalan Raya Gunung Putri, RT 03/RW 07, Gunung Putri, Kabupaten Bogor',
                'address' => [
                    'road' => 'Jalan Raya Gunung Putri',
                    'city_district' => 'Gunung Putri',
                    'county' => 'Kabupaten Bogor',
                ],
            ]),
        ]);

        $geocoder = app(OpenStreetMapGeocodingService::class);
        $address = $geocoder->reverse(-6.4239, 106.9401);
        $cachedAddress = $geocoder->reverse(-6.4239, 106.9401);

        $this->assertSame([
            'kabupaten' => 'Kabupaten Bogor',
            'kecamatan' => 'Gunung Putri',
            'jalan' => 'Jalan Raya Gunung Putri',
            'rt' => '03',
            'rw' => '07',
        ], $address);
        $this->assertSame($address, $cachedAddress);
        Http::assertSentCount(1);
    }

    public function test_reverse_geocoder_does_not_guess_missing_rt_rw(): void
    {
        Cache::flush();
        Http::fake([
            'nominatim.openstreetmap.org/reverse*' => Http::response([
                'address' => [
                    'road' => 'Jalan Raya Gunung Putri',
                    'city_district' => 'Gunung Putri',
                    'county' => 'Kabupaten Bogor',
                ],
            ]),
        ]);

        $address = app(OpenStreetMapGeocodingService::class)->reverse(-6.4200, 106.9400);

        $this->assertSame('', $address['rt']);
        $this->assertSame('', $address['rw']);
    }

    public function test_reverse_geocoder_returns_null_when_provider_fails(): void
    {
        Cache::flush();
        Http::fake([
            'nominatim.openstreetmap.org/reverse*' => Http::response([], 503),
        ]);

        $address = app(OpenStreetMapGeocodingService::class)->reverse(-6.4239, 106.9401);

        $this->assertNull($address);
    }

    /**
     * Uji Algoritma 2: Rule-based Pricing & Durasi (Dasar Biaya Transportasi Jarak Kantor Pusat ke Rumah User)
     * Skenario 1: Jarak kantor pusat ke rumah user 3.0 km (bebas biaya karena <= 5.0 km)
     * Skenario 2: Jarak kantor pusat ke rumah user 9.0 km (kena biaya tambahan 4.0 km x 2.500 = Rp 10.000)
     */
    public function test_rule_based_pricing_calculation(): void
    {
        // Uji helper hitung jarak kantor pusat ke rumah user
        $distKantor = $this->pricing->calculateDistanceToKantor(-6.2000, 106.8000, -6.1730, 106.8000);
        $this->assertEquals(3.0, $distKantor, 'Jarak dari kantor pusat (-6.1730) ke rumah user (-6.2000) harus 3.0 km');

        // Skenario 1: 3.0 km
        $res = $this->pricing->calculate(
            hargaMin: 100000,
            hargaMax: 200000,
            durasiDasarMenit: 60,
            kompleksitas: 'sedang',
            urgensi: 'mendesak',
            jarakKm: 3.0
        );

        $this->assertEquals(150000, $res['harga_layanan'], 'Harga layanan kompleksitas sedang harus 150.000');
        $this->assertEquals(1.20, $res['faktor_urgensi'], 'Faktor urgensi mendesak harus 1.20');
        $this->assertEquals(0, $res['biaya_jarak'], 'Biaya jarak di bawah 5 km harus Rp0 (gratis)');
        $this->assertEquals(0, $res['biaya_transportasi'], 'Biaya transportasi harus Rp0');
        $this->assertEquals(3.0, $res['jarak_kantor_km'], 'Jarak kantor pusat ke user harus 3.0 km');
        $this->assertEquals(180000, $res['estimasi_total'], 'Total estimasi harus 180.000');
        $this->assertEquals(60, $res['durasi_kerja_menit'], 'Durasi layanan harus mengikuti durasi yang dibooking');
        $this->assertEquals(6, $res['durasi_tempuh_menit'], 'Durasi tempuh harus 6 menit');
        $this->assertEquals(60, $res['estimasi_durasi_menit'], 'Durasi layanan tidak ditambah waktu tempuh');

        $damageEstimate = $this->pricing->calculate(
            hargaMin: 100000,
            hargaMax: 100000,
            durasiDasarMenit: 60,
            kompleksitas: 'rendah',
            urgensi: 'darurat',
            jarakKm: 9.0,
            tingkatKerusakan: 'berat'
        );
        $this->assertEquals(1.40, $damageEstimate['faktor_urgensi']);
        $this->assertEquals(1.10, $damageEstimate['faktor_kerusakan']);
        $this->assertEquals(40000, $damageEstimate['biaya_urgensi']);
        $this->assertEquals(10000, $damageEstimate['biaya_kerusakan']);
        $this->assertEquals(160000, $damageEstimate['estimasi_total']);

        $qualifiedDamageEstimate = $this->pricing->calculate(
            hargaMin: 150000,
            hargaMax: 150000,
            durasiDasarMenit: 60,
            kompleksitas: 'rendah',
            urgensi: 'darurat',
            tingkatKerusakan: 'berat',
            tarifDasarPerhitungan: 100000
        );
        $this->assertEquals(40000, $qualifiedDamageEstimate['biaya_urgensi']);
        $this->assertEquals(10000, $qualifiedDamageEstimate['biaya_kerusakan']);
        $this->assertEquals(200000, $qualifiedDamageEstimate['estimasi_total']);

        // Skenario 2: 9.0 km (biaya tambahan transportasi dari kantor pusat)
        $res2 = $this->pricing->calculate(
            hargaMin: 100000,
            hargaMax: 200000,
            durasiDasarMenit: 60,
            kompleksitas: 'sedang',
            urgensi: 'normal',
            jarakKm: 9.0
        );
        $this->assertEquals(10000, $res2['biaya_jarak'], 'Biaya jarak 9 km (4 km x 2.500) harus Rp 10.000');
        $this->assertEquals(160000, $res2['estimasi_total'], 'Total estimasi: 150.000 + 10.000 = 160.000');

        $routeEstimate = $this->pricing->calculate(
            hargaMin: 100000,
            hargaMax: 200000,
            durasiDasarMenit: 60,
            kompleksitas: 'tinggi',
            jarakKm: 1.0,
            userLat: -6.4239,
            userLon: 106.9401,
            routeDistanceKm: 9.0,
            routeDurationMinutes: 18
        );
        $this->assertEquals(9.0, $routeEstimate['jarak_kantor_km']);
        $this->assertEquals(10000, $routeEstimate['biaya_jarak']);
        $this->assertEquals(18, $routeEstimate['durasi_tempuh_menit']);
        $this->assertEquals(60, $routeEstimate['estimasi_durasi_menit'], 'Durasi layanan tetap mengikuti layanan yang dibooking');
        $this->assertSame('jarak_jalan_osrm', $routeEstimate['tipe_jarak']);
    }

    /**
     * Uji ranking tukang berdasarkan rating tertinggi.
     */
    public function test_worker_scoring_and_ranking(): void
    {
        // 1. Bersihkan tukang aktif testing terdahulu jika ada
        DB::table('workers')->whereIn('name', ['Tukang A (Test)', 'Tukang B (Test)'])->delete();

        // 2. Siapkan data Tukang A (Rating tinggi, job aktif 0)
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

        // Pengalaman Tukang B lebih tinggi, tetapi rating lebih rendah.
        $idB = DB::table('workers')->insertGetId([
            'name' => 'Tukang B (Test)',
            'role' => 'Teknisi AC',
            'alamat' => 'Jakarta Utara',
            'phone' => '081234567892',
            'skills' => 'AC, Pendingin',
            'pengalaman' => 50,
            'rating' => 4.50,
            'latitude' => -6.1191,
            'longitude' => 106.8000,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Jalankan ranking untuk lokasi user di kantor pusat TEKSIKA.
        $ranked = $this->scoring->rankWorkers('AC', -6.4239, 106.9401);

        // Filter kandidat test kita
        $filtered = array_values(array_filter($ranked, function ($item) use ($idA, $idB) {
            return in_array($item['id_worker'], [$idA, $idB]);
        }));

        $this->assertCount(2, $filtered, 'Harus ada 2 kandidat tukang yang memenuhi kualifikasi');

        $this->assertEquals($idA, $filtered[0]['id_worker'], 'Rating tertinggi harus selalu mendapat peringkat pertama.');
        $this->assertEquals($filtered[0]['rating'] / 5, $filtered[0]['skor_total']);
        $this->assertEquals($filtered[1]['rating'] / 5, $filtered[1]['skor_total']);

        // Jarak kantor pusat ke rumah user harus identik untuk kedua tukang
        $this->assertEquals($filtered[0]['jarak_km'], $filtered[1]['jarak_km'], 'Jarak kantor pusat ke rumah user harus sama untuk semua tukang');
        $this->assertEquals($filtered[0]['skor_jarak'], $filtered[1]['skor_jarak'], 'Skor jarak harus sama untuk semua tukang');

        // 5. Uji jika user berada di luar radius operasional kantor pusat (misal: Bogor, > 40 km)
        $rankedJauh = $this->scoring->rankWorkers('AC', -6.5971, 106.8060);
        $this->assertEmpty($rankedJauh, 'User di luar radius 15 km dari kantor pusat tidak boleh dilayani');
    }
}
