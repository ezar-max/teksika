<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PricingTransparencyTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->seed(\Database\Seeders\PricingRuleSeeder::class);
    }

    public function test_request_form_shows_cost_breakdown_and_saves_damage_level(): void
    {
        Http::fake([
            'router.project-osrm.org/*' => Http::response([
                'code' => 'Ok',
                'routes' => [[
                    'distance' => 8400,
                    'duration' => 1200,
                    'geometry' => ['coordinates' => [[106.9401, -6.4239], [106.9500, -6.4200]]],
                ]],
            ]),
        ]);
        $user = User::create([
            'name' => 'Pricing Test Customer',
            'email' => uniqid('pricing-customer-', true) . '@example.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
        DB::table('services')->insert([
            'nama' => 'Pricing Transparency Test',
            'deskripsi' => 'Test price breakdown',
            'harga_min' => 100000,
            'harga_max' => 200000,
            'durasi_dasar_menit' => 60,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user, 'web')
            ->get(route('request'))
            ->assertOk()
            ->assertSee('Transparansi Estimasi Biaya')
            ->assertSee('Tarif dasar layanan')
            ->assertSee('Penyesuaian rating tukang')
            ->assertSee('Penyesuaian pengalaman tukang')
            ->assertSee('Biaya jasa tukang')
            ->assertSee('Jarak lokasi ke tukang')
            ->assertSee('Tarif transportasi per km')
            ->assertDontSee('Biaya poin')
            ->assertDontSee('Estimasi pengerjaan')
            ->assertSee('Tingkat Kerusakan')
            ->assertSee('Gangguan kecil; fungsi utama masih berjalan.')
            ->assertSee('Fungsi terganggu atau sebagian tidak berjalan.')
            ->assertSee('Kerusakan parah, tidak berfungsi, atau berisiko.')
            ->assertSee('akan diverifikasi admin sebelum penawaran final')
            ->assertSee('Foto Kerusakan')
            ->assertSee('Wajib untuk verifikasi tingkat kerusakan oleh admin')
            ->assertSee('Cari Lokasi')
            ->assertSee('id="locationDetails"', false)
            ->assertDontSee('Garis Lurus');

        Storage::fake('public');
        $requestData = [
            'layanan' => 'Pricing Transparency Test',
            'lokasi' => 'Jalan Test, Kecamatan Gunung Putri',
            'latitude' => -6.4200,
            'longitude' => 106.9500,
            'urgensi' => 'sedang',
            'tingkat_kerusakan' => 'berat',
            'detail' => 'Kerusakan perlu diperiksa.',
            'metode_pembayaran' => 'COD',
        ];
        $this->actingAs($user, 'web')
            ->post(route('user.request.store'), $requestData)
            ->assertSessionHasErrors('foto');
        $this->assertDatabaseMissing('request', [
            'id_user' => $user->id,
            'layanan' => 'Pricing Transparency Test',
        ]);

        $this->actingAs($user, 'web')
            ->post(route('user.request.store'), array_merge($requestData, [
                'foto' => [$this->fakeDamagePhoto()],
            ]))
            ->assertRedirect(route('bookinguser'));

        $this->assertDatabaseHas('request', [
            'id_user' => $user->id,
            'layanan' => 'Pricing Transparency Test',
            'urgensi' => 'sedang',
            'tingkat_kerusakan' => 'berat',
            'biaya_additional_estimasi' => 0,
            'biaya_rating_estimasi' => 0,
            'biaya_pengalaman_estimasi' => 0,
            'estimasi_durasi_menit' => 60,
        ]);

        $estimate = DB::table('request')->where('id_user', $user->id)->latest('id_request')->first();
        $this->assertStringContainsString('request_foto/', $estimate->foto);
        $this->assertEquals(8.4, $estimate->jarak_km);
        $this->get(route('bookinguser'))
            ->assertOk()
            ->assertSee('Estimasi awal:')
            ->assertSee('data-status-harga="Estimasi awal memakai harga layanan karena belum ada tukang yang cocok"', false)
            ->assertSee('data-total-penawaran="' . number_format((float) $estimate->estimasi_total, 2, '.', '') . '"', false)
            ->assertSee('data-jarak="8.40"', false)
            ->assertSee('data-jarak-label="Jarak rute berkendara"', false)
            ->assertDontSee('data-poin=', false)
            ->assertDontSee('Biaya poin', false);
    }

    public function test_booking_detail_shows_worker_distance_and_saved_price_breakdown(): void
    {
        $user = User::create([
            'name' => 'Booking Detail Customer',
            'email' => uniqid('booking-detail-', true) . '@example.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
        $workerId = DB::table('workers')->insertGetId([
            'name' => 'Tukang Pilihan Snapshot',
            'role' => 'Teknisi',
            'alamat' => 'Gunung Putri',
            'phone' => '08000000000',
            'skills' => 'Listrik',
            'tarif' => 100000,
            'pengalaman' => 4,
            'rating' => 4.5,
            'status' => 'aktif',
            'status_ketersediaan' => 'siap',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $requestId = DB::table('request')->insertGetId([
            'id_user' => $user->id,
            'id_worker_pilihan' => $workerId,
            'id_worker' => $workerId,
            'layanan' => 'Listrik',
            'lokasi' => 'Jalan Test, Kecamatan Gunung Putri',
            'latitude' => -6.42,
            'longitude' => 106.95,
            'urgensi' => 'sedang',
            'tingkat_kerusakan' => 'berat',
            'kompleksitas' => 'sedang',
            'detail' => 'Keterangan kerusakan untuk detail booking.',
            'status' => 'dikonfirmasi',
            'status_admin' => 'diterima',
            'tanggal' => now(),
            'metode_pembayaran' => 'COD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $offerId = DB::table('worker_offers')->insertGetId([
            'id_request' => $requestId,
            'id_worker' => $workerId,
            'biaya_jasa_material' => 242500,
            'biaya_jasa_snapshot' => 242500,
            'biaya_material_snapshot' => 0,
            'harga_dasar_snapshot' => 100000,
            'biaya_kualifikasi_snapshot' => 212500,
            'biaya_rating_snapshot' => 112500,
            'biaya_pengalaman_snapshot' => 100000,
            'pengalaman_persen_snapshot' => 100,
            'biaya_additional_snapshot' => 0,
            'biaya_urgensi_snapshot' => 20000,
            'biaya_kerusakan_snapshot' => 10000,
            'biaya_transportasi' => 10000,
            'total_estimasi' => 252500,
            'jarak_km' => 15,
            'estimasi_durasi_menit' => 90,
            'rating_snapshot' => 4.5,
            'pengalaman_snapshot' => 4,
            'jumlah_ulasan_snapshot' => 0,
            'keahlian_snapshot' => 'Listrik',
            'faktor_harga_snapshot' => 2.425,
            'status' => 'ditugaskan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('request')->where('id_request', $requestId)->update([
            'id_penawaran_terpilih' => $offerId,
            'estimasi_total' => 252500,
            'jumlah_pembayaran' => 252500,
        ]);

        $this->actingAs($user, 'web')
            ->get(route('bookinguser'))
            ->assertOk()
            ->assertSee('Tukang Pilihan Snapshot')
            ->assertSee('data-kerusakan="berat"', false)
            ->assertSee('data-urgensi="sedang"', false)
            ->assertDontSee('data-urgensi-target=', false)
            ->assertSee('data-jarak="15.00"', false)
            ->assertSee('data-durasi-layanan="90"', false)
            ->assertSee('data-harga-dasar="100000.00"', false)
            ->assertSee('data-biaya-rating="112500.00"', false)
            ->assertSee('data-biaya-pengalaman="100000.00"', false)
            ->assertDontSee('data-poin=', false)
            ->assertSee('data-biaya-urgensi="20000.00"', false)
            ->assertSee('data-biaya-kerusakan="10000.00"', false)
            ->assertDontSee('Biaya poin', false)
            ->assertSee('Transparansi harga');
    }

    public function test_estimate_endpoint_matches_stored_request_and_reflects_admin_rule_changes(): void
    {
        Http::fake([
            'router.project-osrm.org/*' => Http::response([
                'code' => 'Ok',
                'routes' => [[
                    'distance' => 9000,
                    'duration' => 900,
                    'geometry' => ['coordinates' => [[106.9401, -6.4239], [106.9500, -6.4200]]],
                ]],
            ]),
        ]);

        $user = User::create([
            'name' => 'Consistency Customer',
            'email' => uniqid('consistency-', true) . '@example.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        DB::table('services')->insert([
            'nama' => 'AC Konsistensi',
            'deskripsi' => 'Uji konsistensi backend & frontend',
            'harga_min' => 1000000,
            'harga_max' => 1500000,
            'durasi_dasar_menit' => 60,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $workerId = DB::table('workers')->insertGetId([
            'name' => 'Teknisi Konsisten',
            'role' => 'Teknisi AC',
            'alamat' => 'Gunung Putri',
            'phone' => '08111111111',
            'skills' => 'AC Konsistensi',
            'tarif' => 200000,
            'pengalaman' => 10,
            'rating' => 3.0,
            'status' => 'aktif',
            'status_ketersediaan' => 'siap',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estimateResponse = $this->actingAs($user, 'web')
            ->postJson(route('user.request.estimate'), [
                'layanan' => 'AC Konsistensi',
                'id_worker_pilihan' => $workerId,
                'urgensi' => 'sedang',
                'tingkat_kerusakan' => 'berat',
                'distance_km' => 9.0,
            ]);

        $estimateResponse->assertOk()->assertJsonPath('success', true);
        $estimate = $estimateResponse->json('estimate');

        // Rating 3.0 -> 9% * 200.000 = 18.000; Pengalaman 10 thn -> 20% * 200.000 = 40.000
        // Biaya tukang = 1.000.000 + 18.000 + 40.000 = 1.058.000
        // Urgensi sedang (+20% * 1.000.000 = 200.000), Kerusakan berat (+10% * 1.000.000 = 100.000)
        // Subtotal jasa = 1.358.000, Transportasi = (9 - 5) * 2.500 = 10.000, Total = 1.368.000
        $this->assertEquals(1000000, $estimate['tarif_dasar_layanan']);
        $this->assertEquals(18000, $estimate['biaya_rating']);
        $this->assertEquals(40000, $estimate['biaya_pengalaman']);
        $this->assertEquals(1058000, $estimate['biaya_tukang']);
        $this->assertEquals(200000, $estimate['biaya_urgensi']);
        $this->assertEquals(100000, $estimate['biaya_kerusakan']);
        $this->assertEquals(1358000, $estimate['subtotal_jasa']);
        $this->assertEquals(10000, $estimate['biaya_transportasi']);
        $this->assertEquals(1368000, $estimate['estimasi_total']);

        Storage::fake('public');
        $this->actingAs($user, 'web')
            ->post(route('user.request.store'), [
                'layanan' => 'AC Konsistensi',
                'id_worker_pilihan' => $workerId,
                'lokasi' => 'Jalan Konsisten, Gunung Putri',
                'latitude' => -6.4200,
                'longitude' => 106.9500,
                'urgensi' => 'sedang',
                'tingkat_kerusakan' => 'berat',
                'detail' => 'Tes konsistensi penuh.',
                'metode_pembayaran' => 'COD',
                'foto' => [$this->fakeDamagePhoto()],
            ])
            ->assertRedirect(route('bookinguser'));

        $saved = DB::table('request')->where('id_user', $user->id)->latest('id_request')->first();
        $this->assertEquals($estimate['tarif_dasar_layanan'], (float) $saved->harga_dasar_estimasi);
        $this->assertEquals($estimate['biaya_rating'], (float) $saved->biaya_rating_estimasi);
        $this->assertEquals($estimate['biaya_pengalaman'], (float) $saved->biaya_pengalaman_estimasi);
        $this->assertEquals($estimate['biaya_tukang'], (float) $saved->harga_layanan);
        $this->assertEquals($estimate['biaya_urgensi'], (float) $saved->biaya_urgensi_estimasi);
        $this->assertEquals($estimate['biaya_kerusakan'], (float) $saved->biaya_kerusakan_estimasi);
        $this->assertEquals($estimate['biaya_transportasi'], (float) $saved->biaya_jarak);
        $this->assertEquals($estimate['estimasi_total'], (float) $saved->estimasi_total);
    }

    private function fakeDamagePhoto(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'kerusakan.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
        );
    }
}
