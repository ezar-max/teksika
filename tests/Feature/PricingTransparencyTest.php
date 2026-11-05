<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PricingTransparencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_request_form_shows_cost_breakdown_and_saves_damage_level(): void
    {
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
            ->assertSee('Tingkat Kerusakan');

        $this->actingAs($user, 'web')
            ->post(route('user.request.store'), [
                'layanan' => 'Pricing Transparency Test',
                'lokasi' => 'Jalan Test, Kecamatan Gunung Putri',
                'latitude' => -6.4200,
                'longitude' => 106.9500,
                'urgensi' => 'sedang',
                'tingkat_kerusakan' => 'berat',
                'detail' => 'Kerusakan perlu diperiksa.',
                'metode_pembayaran' => 'COD',
            ])
            ->assertRedirect(route('bookinguser'));

        $this->assertDatabaseHas('request', [
            'id_user' => $user->id,
            'layanan' => 'Pricing Transparency Test',
            'urgensi' => 'sedang',
            'tingkat_kerusakan' => 'berat',
        ]);

        $estimate = DB::table('request')->where('id_user', $user->id)->latest('id_request')->first();
        $this->get(route('bookinguser'))
            ->assertOk()
            ->assertSee('Estimasi awal:')
            ->assertSee('data-status-harga="Estimasi awal memakai harga layanan karena belum ada tukang yang cocok"', false)
            ->assertSee('data-total-penawaran="' . number_format((float) $estimate->estimasi_total, 2, '.', '') . '"', false);
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
            'biaya_jasa_material' => 155000,
            'biaya_jasa_snapshot' => 155000,
            'biaya_material_snapshot' => 0,
            'harga_dasar_snapshot' => 100000,
            'biaya_urgensi_snapshot' => 20000,
            'biaya_kerusakan_snapshot' => 35000,
            'biaya_transportasi' => 10000,
            'total_estimasi' => 165000,
            'jarak_km' => 15,
            'estimasi_durasi_menit' => 90,
            'rating_snapshot' => 4.5,
            'pengalaman_snapshot' => 4,
            'jumlah_ulasan_snapshot' => 0,
            'keahlian_snapshot' => 'Listrik',
            'faktor_harga_snapshot' => 1.55,
            'status' => 'ditugaskan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('request')->where('id_request', $requestId)->update([
            'id_penawaran_terpilih' => $offerId,
            'estimasi_total' => 165000,
            'jumlah_pembayaran' => 165000,
        ]);

        $this->actingAs($user, 'web')
            ->get(route('bookinguser'))
            ->assertOk()
            ->assertSee('Tukang Pilihan Snapshot')
            ->assertSee('data-kerusakan="berat"', false)
            ->assertSee('data-urgensi="sedang"', false)
            ->assertSee('data-jarak="15.00"', false)
            ->assertSee('data-harga-dasar="100000.00"', false)
            ->assertSee('data-biaya-urgensi="20000.00"', false)
            ->assertSee('data-biaya-kerusakan="35000.00"', false)
            ->assertSee('Transparansi harga');
    }

    public function test_locate_address_accepts_single_query_and_returns_location(): void
    {
        $user = User::create([
            'name' => 'Locate Test User',
            'email' => uniqid('locate-user-', true) . '@example.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        \Illuminate\Support\Facades\Http::fake([
            'nominatim.openstreetmap.org/search*' => \Illuminate\Support\Facades\Http::response([
                [
                    'lat' => '-6.365432',
                    'lon' => '107.123456',
                    'display_name' => 'GIIC Blok BB, Cikarang Pusat, Kabupaten Bekasi, Indonesia',
                ],
            ]),
        ]);

        \Illuminate\Support\Facades\Cache::flush();

        $response = $this->actingAs($user, 'web')
            ->postJson(route('user.request.lokasi-alamat'), [
                'query' => 'GIIC Blok BB, Cikarang Pusat',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'location' => [
                    'latitude' => -6.365432,
                    'longitude' => 107.123456,
                    'display_name' => 'GIIC Blok BB, Cikarang Pusat, Kabupaten Bekasi, Indonesia',
                ],
            ]);
    }
}
