<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuoteApprovalWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PricingRuleSeeder::class);
        Cache::flush();
    }

    public function test_admin_creates_per_worker_offers_and_user_selects_one(): void
    {
        Cache::flush();
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

        $user = $this->createCustomer();
        $admin = $this->createAdmin();
        $serviceId = DB::table('services')->insertGetId([
            'nama' => 'Listrik Workflow Test',
            'deskripsi' => 'Test quote service',
            'harga_min' => 100000,
            'harga_max' => 200000,
            'durasi_dasar_menit' => 60,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $workerOneId = $this->createWorker('Offer Worker One', 'Listrik', 3, 3.5, 120000);
        $workerTwoId = $this->createWorker('Offer Worker Two', 'Listrik', 9, 4.9, 180000);
        DB::table('workers')->where('id', $workerOneId)->update(['poin' => 20]);
        DB::table('workers')->where('id', $workerTwoId)->update(['poin' => 50]);
        $requestId = $this->createRequest($user, ['layanan' => 'Listrik Workflow Test']);

        $this->actingAs($admin, 'admin')
            ->from(route('admin.booking.show', $requestId))
            ->post(route('admin.booking.penawaran', $requestId), [
                'tingkat_kerusakan' => 'sedang',
                'kompleksitas' => 'sedang',
                'urgensi' => 'rendah',
                'penawaran_keahlian' => 'Instalasi listrik',
                'biaya_material' => 50000,
                'penawaran_catatan' => 'Penggantian komponen dan pengecekan jalur.',
            ])
            ->assertRedirect(route('admin.booking.show', $requestId));

        $booking = DB::table('request')->where('id_request', $requestId)->first();
        $offers = DB::table('worker_offers')->where('id_request', $requestId)->orderBy('id')->get();
        $this->assertSame('menunggu_persetujuan', $booking->status);
        $this->assertSame('Instalasi listrik', $booking->penawaran_keahlian);
        $this->assertEquals(50000, $booking->biaya_material);
        $this->assertNull($booking->estimasi_total);
        $this->assertNotNull($booking->penawaran_dikirim_at);
        $this->assertGreaterThanOrEqual(2, $offers->count());
        $this->assertLessThanOrEqual(3, $offers->count());
        foreach ($offers as $offer) {
            $this->assertEquals($offer->biaya_jasa_material + $offer->biaya_transportasi + $offer->biaya_additional_snapshot, $offer->total_estimasi);
            $this->assertEquals($offer->biaya_jasa_snapshot + $offer->biaya_material_snapshot, $offer->biaya_jasa_material);
            $workerTarif = DB::table('workers')->where('id', $offer->id_worker)->value('tarif');
            $expectedTarif = (float) $workerTarif > 0 ? (float) $workerTarif : 100000;
            $experiencePercent = (int) $offer->pengalaman_snapshot * 25;
            $ratingFee = round($expectedTarif * (float) $offer->rating_snapshot * 0.25);
            $experienceFee = round($expectedTarif * (int) $offer->pengalaman_snapshot * 0.25);
            $qualificationFee = $ratingFee + $experienceFee;
            $adjustedTarif = $qualificationFee;
            $this->assertEquals($expectedTarif, $offer->harga_dasar_snapshot);
            $this->assertSame(0, (int) $offer->poin_snapshot);
            $this->assertEquals($ratingFee, $offer->biaya_rating_snapshot);
            $this->assertEquals($experienceFee, $offer->biaya_pengalaman_snapshot);
            $this->assertEquals($experiencePercent, $offer->pengalaman_persen_snapshot);
            $this->assertEquals(0, $offer->biaya_additional_snapshot);
            $this->assertEquals($qualificationFee, $offer->biaya_kualifikasi_snapshot);
            $this->assertEquals(0, $offer->biaya_urgensi_snapshot);
            $this->assertEquals(round($expectedTarif * 0.05), $offer->biaya_kerusakan_snapshot);
            $this->assertEquals($adjustedTarif + $offer->biaya_urgensi_snapshot + $offer->biaya_kerusakan_snapshot, $offer->biaya_jasa_snapshot);
            $this->assertSame('ditawarkan', $offer->status);
        }
        $this->assertGreaterThan(1, $offers->pluck('biaya_jasa_material')->unique()->count());
        $this->get(route('admin.booking.show', $requestId))
            ->assertOk()
            ->assertSee('Penawaran per Tukang')
            ->assertSee('Verifikasi tingkat kerusakan saat pemeriksaan');
        $this->get(route('booking'))->assertOk()->assertSee('Menunggu Persetujuan User');

        Auth::guard('admin')->logout();
        $chosenOffer = $offers[1];
        $this->actingAs($user, 'web')
            ->post(route('bookinguser.pilih-penawaran', $requestId), ['offer_id' => $chosenOffer->id])
            ->assertRedirect(route('bookinguser'));

        $this->get(route('bookinguser'))->assertOk()->assertSee('Pilihan Anda');

        $this->assertDatabaseHas('request', [
            'id_request' => $requestId,
            'status' => 'persetujuan_dikirim',
            'id_penawaran_terpilih' => $chosenOffer->id,
            'id_worker' => null,
            'estimasi_total' => null,
        ]);
        $this->assertNotNull(DB::table('request')->where('id_request', $requestId)->value('persetujuan_user_at'));

        DB::table('services')->where('id', $serviceId)->delete();
    }

    public function test_admin_cannot_assign_worker_before_user_approval(): void
    {
        $user = $this->createCustomer();
        $admin = $this->createAdmin();
        $workerId = $this->createWorker('Worker Workflow Test', 'Listrik', 5, 4.5);
        $requestId = $this->createRequest($user, [
            'status' => 'menunggu_persetujuan',
            'penawaran_dikirim_at' => now(),
            'estimasi_total' => 150000,
        ]);

        $this->actingAs($admin, 'admin')
            ->from(route('admin.booking.show', $requestId))
            ->post(route('admin.booking.tugaskan', $requestId))
            ->assertRedirect(route('admin.booking.show', $requestId));

        $this->assertDatabaseHas('request', [
            'id_request' => $requestId,
            'status' => 'menunggu_persetujuan',
            'status_admin' => 'belum_diterima',
            'id_worker' => null,
        ]);
    }

    public function test_admin_can_assign_worker_after_user_approval_without_changing_quote(): void
    {
        $user = $this->createCustomer();
        $admin = $this->createAdmin();
        $workerId = $this->createWorker('Approved Workflow Worker', 'Listrik', 6, 4.8);
        DB::table('workers')->where('id', $workerId)->update(['poin' => 20]);
        $requestId = $this->createRequest($user, [
            'status' => 'menunggu_persetujuan',
            'penawaran_dikirim_at' => now(),
        ]);
        $offerId = DB::table('worker_offers')->insertGetId([
            'id_request' => $requestId,
            'id_worker' => $workerId,
            'biaya_jasa_material' => 175000,
            'biaya_jasa_snapshot' => 125000,
            'biaya_material_snapshot' => 50000,
            'poin_snapshot' => 20,
            'biaya_additional_snapshot' => 2000,
            'biaya_transportasi' => 10000,
            'total_estimasi' => 187000,
            'jarak_km' => 9,
            'estimasi_durasi_menit' => 120,
            'rating_snapshot' => 4.8,
            'pengalaman_snapshot' => 6,
            'jumlah_ulasan_snapshot' => 0,
            'keahlian_snapshot' => 'Listrik',
            'faktor_harga_snapshot' => 1.078,
            'status' => 'ditawarkan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user, 'web')->post(route('bookinguser.pilih-penawaran', $requestId), ['offer_id' => $offerId]);
        Auth::guard('web')->logout();

        $this->actingAs($admin, 'admin')
            ->from(route('admin.booking.show', $requestId))
            ->post(route('admin.booking.tugaskan', $requestId))
            ->assertRedirect(route('admin.booking.show', $requestId));

        $this->assertDatabaseHas('request', [
            'id_request' => $requestId,
            'id_worker' => $workerId,
            'status' => 'dikonfirmasi',
            'status_admin' => 'diterima',
            'estimasi_total' => 187000,
            'jumlah_pembayaran' => 187000,
            'harga_layanan' => 125000,
            'biaya_additional_estimasi' => 2000,
            'biaya_material' => 50000,
            'biaya_jarak' => 10000,
        ]);
        $this->assertNotNull(DB::table('request')->where('id_request', $requestId)->value('verifikasi_admin_at'));
        $this->assertSame('sedang_bekerja', DB::table('workers')->where('id', $workerId)->value('status_ketersediaan'));
        $assignedRequest = DB::table('request')->where('id_request', $requestId)->first();
        $this->assertEquals(
            $assignedRequest->harga_layanan + $assignedRequest->biaya_material + $assignedRequest->biaya_jarak + $assignedRequest->biaya_additional_estimasi,
            $assignedRequest->estimasi_total
        );

        Auth::guard('admin')->logout();
        $this->actingAs($user, 'web')->post(route('bookinguser.ulasan', $requestId), ['rating' => 5])
            ->assertRedirect(route('bookinguser'));
        $this->assertDatabaseMissing('worker_reviews', ['id_request' => $requestId]);
        $this->get(route('bookinguser'))->assertOk()->assertSee('Bayar Rp 187.000');
        Auth::guard('web')->logout();
        $this->actingAs($admin, 'admin');
        $this->get(route('admin.booking.show', $requestId))
            ->assertOk()
            ->assertSee('Menunggu pemesan menyelesaikan pembayaran');

        $this->post(route('booking.status', $requestId), ['status' => 'dikerjakan'])
            ->assertRedirect(route('admin.booking.show', $requestId));
        $this->assertDatabaseHas('request', [
            'id_request' => $requestId,
            'status' => 'dikonfirmasi',
            'bayar_sekarang' => 0,
        ]);

        Auth::guard('admin')->logout();
        $this->actingAs($user, 'web')
            ->post(route('pembayaran.store'), [
                'booking_kode' => 'BK-' . str_pad($requestId, 3, '0', STR_PAD_LEFT),
                'jumlah' => 187000,
                'metode_pembayaran' => 'Tunai',
                'nama_pembayar' => $user->name,
            ])
            ->assertRedirect(route('bookinguser'));
        $this->assertDatabaseHas('request', [
            'id_request' => $requestId,
            'bayar_sekarang' => 1,
        ]);
        $this->assertDatabaseHas('pembayarans', [
            'user_id' => $user->id,
            'jumlah' => 187000,
            'status' => 'success',
        ]);
        $this->get(route('bookinguser'))->assertOk()->assertSee('Pembayaran lunas');

        Auth::guard('web')->logout();
        $this->actingAs($admin, 'admin');
        $this->post(route('booking.status', $requestId), ['status' => 'dikerjakan'])
            ->assertRedirect(route('admin.booking.show', $requestId));
        $this->assertDatabaseHas('request', ['id_request' => $requestId, 'status' => 'dikerjakan']);

        $this->post(route('booking.status', $requestId), ['status' => 'selesai'])
            ->assertRedirect(route('admin.booking.show', $requestId));
        $this->assertDatabaseHas('request', ['id_request' => $requestId, 'status' => 'selesai']);
        $this->assertSame('siap', DB::table('workers')->where('id', $workerId)->value('status_ketersediaan'));

        Auth::guard('admin')->logout();
        $this->actingAs($user, 'web')
            ->post(route('bookinguser.ulasan', $requestId), ['rating' => 5, 'ulasan' => 'Pekerjaan rapi.'])
            ->assertRedirect(route('bookinguser'));
        $this->assertDatabaseHas('worker_reviews', ['id_request' => $requestId, 'id_worker' => $workerId, 'rating' => 5]);
        $this->assertEquals(5, DB::table('workers')->where('id', $workerId)->value('rating'));
        $this->assertSame(20, (int) DB::table('workers')->where('id', $workerId)->value('poin'));
        $this->post(route('bookinguser.ulasan', $requestId), ['rating' => 1, 'ulasan' => 'Duplikat'])
            ->assertRedirect(route('bookinguser'));
        $this->assertSame(1, DB::table('worker_reviews')->where('id_request', $requestId)->count());
    }

    public function test_user_can_cancel_before_approval_but_not_after_sending_approval(): void
    {
        $user = $this->createCustomer();
        $pendingOfferId = $this->createRequest($user, [
            'status' => 'menunggu_persetujuan',
            'penawaran_dikirim_at' => now(),
            'estimasi_total' => 125000,
        ]);

        $this->actingAs($user, 'web')
            ->post(route('bookinguser.batal', $pendingOfferId))
            ->assertRedirect(route('bookinguser'));

        $this->assertDatabaseHas('request', [
            'id_request' => $pendingOfferId,
            'status' => 'dibatalkan',
            'status_admin' => 'dibatalkan',
        ]);

        $approvedId = $this->createRequest($user, [
            'status' => 'persetujuan_dikirim',
            'penawaran_dikirim_at' => now(),
            'persetujuan_user_at' => now(),
            'estimasi_total' => 125000,
        ]);

        $this->from(route('bookinguser'))->post(route('bookinguser.batal', $approvedId));
        $this->assertDatabaseHas('request', [
            'id_request' => $approvedId,
            'status' => 'persetujuan_dikirim',
            'status_admin' => 'belum_diterima',
        ]);
    }

    public function test_request_submission_shows_initial_estimate_without_requiring_osrm(): void
    {
        Http::fake();
        Storage::fake('public');
        $user = $this->createCustomer();
        DB::table('services')->insert([
            'nama' => 'Request Intake Test',
            'deskripsi' => 'Intake only',
            'harga_min' => 100000,
            'harga_max' => 200000,
            'durasi_dasar_menit' => 60,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->createWorker('Request Roster Worker', 'Instalasi', 4, 4.2);

        $this->actingAs($user, 'web')
            ->get(route('request'))
            ->assertOk()
            ->assertSee('Request Roster Worker')
            ->assertSee('Siap Bekerja')
            ->assertSee('(0)')
            ->assertSee('Tingkat Kerusakan');

        $this->actingAs($user, 'web')
            ->post(route('user.request.store'), [
                'layanan' => 'Request Intake Test',
                'lokasi' => 'Jalan Test, Kecamatan Gunung Putri',
                'latitude' => -6.4200,
                'longitude' => 106.9500,
                'urgensi' => 'tinggi',
                'tingkat_kerusakan' => 'berat',
                'detail' => 'Perlu diperiksa oleh admin.',
                'metode_pembayaran' => 'COD',
                'foto' => [$this->fakeDamagePhoto()],
            ])
            ->assertRedirect(route('bookinguser'));

        Http::assertNothingSent();
        $saved = DB::table('request')->where('id_user', $user->id)->latest('id_request')->first();
        $this->assertSame('menunggu', $saved->status);
        $this->assertSame('tinggi', $saved->urgensi);
        $this->assertSame('berat', $saved->tingkat_kerusakan);
        $this->assertEquals(100000, $saved->harga_layanan);
        $this->assertEquals(100000 * (float) DB::table('pricing_rules')->where('key', 'faktor_urgensi_tinggi')->value('value'), $saved->harga_layanan + $saved->biaya_urgensi_estimasi);
        $this->assertGreaterThan(0, $saved->biaya_kerusakan_estimasi);
        $this->assertGreaterThan(0, $saved->estimasi_total);
        $this->assertGreaterThan(0, $saved->jarak_km);
        $this->assertNull($saved->id_worker);
        $this->assertNull($saved->id_worker_estimasi);
    }

    public function test_initial_estimate_uses_the_worker_selected_in_the_request_form(): void
    {
        Http::fake();
        Storage::fake('public');
        $user = $this->createCustomer();
        DB::table('services')->insert([
            'nama' => 'Chosen Worker Estimate Test',
            'deskripsi' => 'Chosen worker estimate',
            'harga_min' => 100000,
            'harga_max' => 200000,
            'durasi_dasar_menit' => 60,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $workerId = $this->createWorker('Form Selected Estimator', 'Chosen Worker Estimate Test', 4, 4.5, 135000);

        $this->actingAs($user, 'web')
            ->post(route('user.request.store'), [
                'layanan' => 'Chosen Worker Estimate Test',
                'id_worker_pilihan' => $workerId,
                'lokasi' => 'Jalan Test, Kecamatan Gunung Putri',
                'latitude' => -6.4200,
                'longitude' => 106.9500,
                'urgensi' => 'sedang',
                'tingkat_kerusakan' => 'berat',
                'detail' => 'Estimasi sesuai teknisi pilihan.',
                'metode_pembayaran' => 'COD',
                'foto' => [$this->fakeDamagePhoto()],
            ])
            ->assertRedirect(route('bookinguser'));

        Http::assertNothingSent();
        $saved = DB::table('request')->where('id_user', $user->id)->latest('id_request')->first();
        $this->assertSame($workerId, (int) $saved->id_worker_pilihan);
        $this->assertSame($workerId, (int) $saved->id_worker_estimasi);
        $this->assertEquals(135000, $saved->harga_dasar_estimasi);
        $this->assertEquals(286875, $saved->biaya_kualifikasi_estimasi);
        $this->assertEquals(286875, $saved->harga_layanan);
        $this->assertGreaterThan(0, $saved->biaya_urgensi_estimasi);
        $this->assertGreaterThan(0, $saved->biaya_kerusakan_estimasi);
        $this->assertEquals(
            $saved->harga_layanan + $saved->biaya_urgensi_estimasi + $saved->biaya_kerusakan_estimasi + $saved->biaya_jarak,
            $saved->estimasi_total
        );
    }

    public function test_completion_does_not_override_worker_leave_status(): void
    {
        $user = $this->createCustomer();
        $admin = $this->createAdmin();
        $workerId = $this->createWorker('Worker On Leave', 'Listrik', 5, 4.5);
        $requestId = $this->createRequest($user, [
            'id_worker' => $workerId,
            'status' => 'dikerjakan',
            'status_admin' => 'diterima',
        ]);
        DB::table('workers')->where('id', $workerId)->update(['status_ketersediaan' => 'libur']);

        $this->actingAs($admin, 'admin')->post(route('booking.status', $requestId), ['status' => 'selesai'])
            ->assertRedirect(route('admin.booking.show', $requestId));

        $this->assertSame('libur', DB::table('workers')->where('id', $workerId)->value('status_ketersediaan'));
    }

    public function test_booking_payment_must_match_approved_total_before_work_can_start(): void
    {
        $user = $this->createCustomer();
        $admin = $this->createAdmin();
        $workerId = $this->createWorker('Prepayment Gate Worker', 'Listrik', 5, 4.5);
        $requestId = $this->createRequest($user, [
            'id_worker' => $workerId,
            'status' => 'dikonfirmasi',
            'status_admin' => 'diterima',
            'jumlah_pembayaran' => 125000,
            'estimasi_total' => 125000,
        ]);

        $this->actingAs($admin, 'admin')
            ->from(route('admin.booking.show', $requestId))
            ->post(route('booking.status', $requestId), ['status' => 'dikerjakan'])
            ->assertRedirect(route('admin.booking.show', $requestId));
        $this->assertDatabaseHas('request', ['id_request' => $requestId, 'status' => 'dikonfirmasi']);

        Auth::guard('admin')->logout();
        $this->actingAs($user, 'web')
            ->post(route('pembayaran.store'), [
                'booking_kode' => 'BK-' . str_pad($requestId, 3, '0', STR_PAD_LEFT),
                'jumlah' => 124999,
                'metode_pembayaran' => 'Tunai',
                'nama_pembayar' => $user->name,
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('request', ['id_request' => $requestId, 'bayar_sekarang' => 0]);
        $this->assertDatabaseMissing('pembayarans', ['user_id' => $user->id]);

        $this->post(route('pembayaran.store'), [
            'booking_kode' => 'BK-' . str_pad($requestId, 3, '0', STR_PAD_LEFT),
            'jumlah' => 125000,
            'metode_pembayaran' => 'Tunai',
            'nama_pembayar' => $user->name,
        ])->assertRedirect(route('bookinguser'));
        $this->assertDatabaseHas('request', ['id_request' => $requestId, 'bayar_sekarang' => 1]);
    }

    public function test_customer_login_replaces_existing_admin_guard_in_same_session(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $this->post(route('admin.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard_admin'));

        $this->post(route('login.post'), [
            'email' => $customer->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard_user'));

        $this->get(route('dashboard_user'))->assertOk();
        $this->get(route('dashboard_admin'))->assertRedirect(route('dashboard_user'));
    }

    public function test_customer_dashboard_limits_notification_payload_without_limiting_booking_count(): void
    {
        $user = $this->createCustomer();

        for ($index = 0; $index < 11; $index++) {
            $createdAt = now()->addSeconds($index);
            $this->createRequest($user, [
                'layanan' => 'Notification ' . $index,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $response = $this->actingAs($user)
            ->get(route('dashboard_user'))
            ->assertOk()
            ->assertSee('"layanan":"Notification 10"', false)
            ->assertDontSee('"layanan":"Notification 0"', false)
            ->assertDontSee('"estimasi_total"', false)
            ->assertSee('Lihat semua booking');

        $this->assertSame(11, \App\Models\Request::where('id_user', $user->id)->count());

        $this->get('/dashboard-user')
            ->assertOk()
            ->assertSee('"layanan":"Notification 10"', false)
            ->assertDontSee('"layanan":"Notification 0"', false);
    }

    private function createCustomer(): User
    {
        return User::create([
            'name' => 'Workflow Customer',
            'email' => uniqid('workflow-customer-', true) . '@example.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }

    private function fakeDamagePhoto(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'kerusakan.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
        );
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'name' => 'Workflow Admin',
            'email' => uniqid('workflow-admin-', true) . '@example.test',
            'password' => Hash::make('password'),
        ]);
    }

    private function createRequest(User $user, array $attributes = []): int
    {
        $requestId = DB::table('request')->insertGetId(array_merge([
            'id_user' => $user->id,
            'layanan' => 'Listrik',
            'lokasi' => 'Jalan Test, Kecamatan Gunung Putri, Kabupaten Bogor',
            'latitude' => -6.4200,
            'longitude' => 106.9500,
            'urgensi' => 'rendah',
            'kompleksitas' => 'sedang',
            'detail' => 'Uji alur penawaran dan persetujuan.',
            'status' => 'menunggu',
            'status_admin' => 'belum_diterima',
            'tanggal' => now(),
            'metode_pembayaran' => 'COD',
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));

        DB::table('request')->where('id_request', $requestId)->update([
            'kode_booking' => 'BK-' . str_pad($requestId, 3, '0', STR_PAD_LEFT),
        ]);

        return $requestId;
    }

    private function createWorker(string $name, string $skills, int $experience, float $rating, ?float $tarif = null): int
    {
        return DB::table('workers')->insertGetId([
            'name' => $name,
            'role' => 'Teknisi ' . $skills,
            'alamat' => 'Gunung Putri',
            'phone' => '08000000000',
            'skills' => $skills,
            'tarif' => $tarif,
            'pengalaman' => $experience,
            'rating' => $rating,
            'status' => 'aktif',
            'status_ketersediaan' => 'siap',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}