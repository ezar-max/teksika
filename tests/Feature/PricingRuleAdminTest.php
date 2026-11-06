<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PricingRuleAdminTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Pricing Test',
            'email' => uniqid('admin-pricing-', true) . '@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_pricing_rules_page(): void
    {
        $response = $this->withSession(['role' => 'admin'])
            ->actingAs($this->admin)
            ->get(route('admin.pricing.index'));

        $response->assertOk()
            ->assertSee('Aturan Tarif &amp; Biaya', false)
            ->assertSee('Radius Jangkauan Maksimal')
            ->assertSee('Tarif Tambahan Per KM')
            ->assertSee('Tarif Rating &amp; Pengalaman Tukang', false)
            ->assertSee('Setiap tahun pengalaman bernilai 0,25')
            ->assertDontSee('Biaya Poin')
            ->assertDontSee('Pengali Durasi')
            ->assertDontSee('Estimasi Kecepatan Tempuh')
            ->assertDontSee('Weighted Scoring')
            ->assertDontSee('Bobot Jarak');
    }

    public function test_admin_can_update_pricing_rules_and_persists_in_database(): void
    {
        DB::table('pricing_rules')->where('key', 'jarak_gratis_km')->delete();
        DB::table('pricing_rules')->whereIn('key', [
            'poin_tarif_persen',
            'pengalaman_per_tahun_persen',
            'pengalaman_batas_persen',
        ])->delete();

        $payload = [
            'jarak_gratis_km' => '6.5',
            'tarif_per_km' => '3500',
            'radius_maksimal_km' => '20.0',
<<<<<<< HEAD
            'bobot_rating' => '0.60',
            'bobot_pengalaman' => '0.40',
=======
>>>>>>> 40abfce1492bc7d6974163b6b4b29023d1a61d3b
            'faktor_urgensi_rendah' => '1.00',
            'faktor_urgensi_sedang' => '1.25',
            'faktor_urgensi_tinggi' => '1.50',
            'faktor_kerusakan_ringan' => '1.00',
            'faktor_kerusakan_sedang' => '1.08',
            'faktor_kerusakan_berat' => '1.15',
        ];

        $response = $this->withSession(['role' => 'admin'])
            ->actingAs($this->admin)
            ->post(route('admin.pricing.update'), $payload);

        $response->assertRedirect(route('admin.pricing.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pricing_rules', [
            'key' => 'radius_maksimal_km',
            'value' => '20.0',
        ]);

        $this->assertDatabaseHas('pricing_rules', [
            'key' => 'tarif_per_km',
            'value' => '3500',
        ]);

        $this->assertDatabaseHas('pricing_rules', [
<<<<<<< HEAD
            'key' => 'bobot_rating',
            'value' => '0.60',
        ]);

        $this->assertDatabaseHas('pricing_rules', [
            'key' => 'bobot_pengalaman',
            'value' => '0.40',
=======
            'key' => 'jarak_gratis_km',
            'value' => '6.5',
            'label' => 'Batas Jarak Gratis (km)',
        ]);
<<<<<<< HEAD
        $this->assertDatabaseHas('pricing_rules', [
            'key' => 'poin_tarif_persen',
            'value' => '2.50',
        ]);
        $this->assertDatabaseHas('pricing_rules', [
            'key' => 'pengalaman_per_tahun_persen',
            'value' => '1.50',
        ]);
        $this->assertDatabaseHas('pricing_rules', [
            'key' => 'pengalaman_batas_persen',
            'value' => '20.00',
>>>>>>> 40abfce1492bc7d6974163b6b4b29023d1a61d3b
        ]);

=======
>>>>>>> cef250ff0e772f677286d0619936d807ab7b7e4a
        // Verifikasi pada halaman index bahwa nilai yang baru tersimpan muncul di kartu atas
        $followUp = $this->withSession(['role' => 'admin'])
            ->actingAs($this->admin)
            ->get(route('admin.pricing.index'));

        $followUp->assertOk()
            ->assertSee('20.0')
            ->assertSee('3.500');
    }

    public function test_pricing_update_no_longer_requires_recommendation_weights(): void
    {
        DB::table('pricing_rules')->whereIn('key', [
            'poin_tarif_persen',
            'pengalaman_per_tahun_persen',
            'pengalaman_batas_persen',
        ])->delete();
        $existingWeight = DB::table('pricing_rules')->where('key', 'bobot_rating')->value('value');
        $payload = [
<<<<<<< HEAD
            'bobot_rating' => '0.70',
            'bobot_pengalaman' => '0.50', // total 1.20 != 1.00
=======
            'poin_tarif_persen' => '2.00',
            'pengalaman_per_tahun_persen' => '1.00',
            'pengalaman_batas_persen' => '25.00',
            'bobot_rating' => '0.99',
>>>>>>> 40abfce1492bc7d6974163b6b4b29023d1a61d3b
        ];

        $response = $this->withSession(['role' => 'admin'])
            ->actingAs($this->admin)
            ->post(route('admin.pricing.update'), $payload);

        $response->assertRedirect(route('admin.pricing.index'))
            ->assertSessionHas('success')
            ->assertSessionMissing('error');
        $this->assertSame(
            $existingWeight,
            DB::table('pricing_rules')->where('key', 'bobot_rating')->value('value')
        );
        $this->assertDatabaseMissing('pricing_rules', ['key' => 'poin_tarif_persen']);
        $this->assertDatabaseMissing('pricing_rules', ['key' => 'pengalaman_per_tahun_persen']);
        $this->assertDatabaseMissing('pricing_rules', ['key' => 'pengalaman_batas_persen']);
    }
}
