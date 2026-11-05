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
            ->assertSee('Aturan Tarif &amp; Parameter Algoritma', false)
            ->assertSee('Radius Jangkauan Maksimal')
            ->assertSee('Tarif Tambahan Per KM')
            ->assertDontSee('Estimasi Kecepatan Tempuh')
            ->assertSee('Status Bobot Rekomendasi');
    }

    public function test_admin_can_update_pricing_rules_and_persists_in_database(): void
    {
        DB::table('pricing_rules')->where('key', 'jarak_gratis_km')->delete();

        $payload = [
            'jarak_gratis_km' => '6.5',
            'tarif_per_km' => '3500',
            'radius_maksimal_km' => '20.0',
            'bobot_jarak' => '0.30',
            'bobot_rating' => '0.20',
            'bobot_pengalaman' => '0.20',
            'bobot_beban_kerja' => '0.30',
            'faktor_urgensi_rendah' => '1.00',
            'faktor_urgensi_sedang' => '1.25',
            'faktor_urgensi_tinggi' => '1.50',
            'faktor_kerusakan_ringan' => '1.00',
            'faktor_kerusakan_sedang' => '1.08',
            'faktor_kerusakan_berat' => '1.15',
            'pengali_durasi_sedang' => '1.60',
            'pengali_durasi_tinggi' => '2.20',
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
            'key' => 'bobot_jarak',
            'value' => '0.30',
        ]);

        $this->assertDatabaseHas('pricing_rules', [
            'key' => 'jarak_gratis_km',
            'value' => '6.5',
            'label' => 'Batas Jarak Gratis (km)',
        ]);

        // Verifikasi pada halaman index bahwa nilai yang baru tersimpan muncul di kartu atas
        $followUp = $this->withSession(['role' => 'admin'])
            ->actingAs($this->admin)
            ->get(route('admin.pricing.index'));

        $followUp->assertOk()
            ->assertSee('20.0')
            ->assertSee('3.500');
    }

    public function test_update_fails_if_total_weights_not_equal_one(): void
    {
        $payload = [
            'bobot_jarak' => '0.50',
            'bobot_rating' => '0.50',
            'bobot_pengalaman' => '0.50',
            'bobot_beban_kerja' => '0.50', // total 2.00 != 1.00
        ];

        $response = $this->withSession(['role' => 'admin'])
            ->actingAs($this->admin)
            ->post(route('admin.pricing.update'), $payload);

        $response->assertSessionHas('error');
    }
}
