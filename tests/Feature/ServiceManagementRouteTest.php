<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceManagementRouteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_services_route_opens_the_services_section_on_admin_dashboard(): void
    {
        $this->withSession(['role' => 'admin'])
            ->get(route('services.index'))
            ->assertRedirect(route('admin.home') . '#services');

        $this->withSession(['role' => 'admin'])
            ->get(route('admin.home'))
            ->assertOk()
            ->assertSee('id="services"', false)
            ->assertSee('Kategori Layanan Tersedia');
    }

    public function test_adding_service_redirects_back_to_services_section(): void
    {
        $this->withSession(['role' => 'admin'])
            ->post(route('services.store'), [
                'nama' => 'Layanan Route Test',
                'deskripsi' => 'Test layanan',
                'harga_min' => 100000,
                'harga_max' => 200000,
                'status' => 'aktif',
            ])
            ->assertRedirect(route('services.index'))
            ->assertSessionHas('success');

        $this->withSession(['role' => 'admin'])
            ->get(route('admin.home'))
            ->assertOk()
            ->assertSee('Layanan Route Test');
    }
}
