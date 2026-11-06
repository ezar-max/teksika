<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WorkerReviewVisibilityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_worker_reviews_are_visible_to_customers_and_admins(): void
    {
        $user = User::create([
            'name' => 'Review Visibility Customer',
            'email' => uniqid('review-customer-', true) . '@example.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
        $workerId = DB::table('workers')->insertGetId([
            'name' => 'Review Visibility Worker',
            'role' => 'Teknisi',
            'alamat' => 'Gunung Putri',
            'phone' => '08000000000',
            'skills' => 'Listrik',
            'pengalaman' => 5,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $requestId = DB::table('request')->insertGetId([
            'id_user' => $user->id,
            'id_worker' => $workerId,
            'layanan' => 'Listrik',
            'lokasi' => 'Jalan Test, Kecamatan Gunung Putri',
            'urgensi' => 'rendah',
            'detail' => 'Perbaikan selesai.',
            'status' => 'selesai',
            'status_admin' => 'diterima',
            'tanggal' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('worker_reviews')->insert([
            'id_request' => $requestId,
            'id_worker' => $workerId,
            'id_user' => $user->id,
            'rating' => 5,
            'ulasan' => 'Teknisi datang tepat waktu dan pekerjaannya rapi.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $url = route('tukang.ulasan', $workerId);
        $this->actingAs($user, 'web')
            ->get($url)
            ->assertOk()
            ->assertSee('Ulasan untuk Review Visibility Worker')
            ->assertSee('Teknisi datang tepat waktu dan pekerjaannya rapi.')
            ->assertSee('Layanan: Listrik')
            ->assertSee('1 ulasan');

        $this->withSession(['role' => 'admin'])
            ->get($url)
            ->assertOk()
            ->assertSee('Teknisi datang tepat waktu dan pekerjaannya rapi.');
    }

    public function test_worker_without_reviews_shows_an_empty_state(): void
    {
        $workerId = DB::table('workers')->insertGetId([
            'name' => 'Worker Without Reviews',
            'role' => 'Teknisi',
            'alamat' => 'Gunung Putri',
            'phone' => '08000000000',
            'skills' => 'Listrik',
            'pengalaman' => 2,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get(route('tukang.ulasan', $workerId))
            ->assertOk()
            ->assertSee('Belum ada ulasan untuk teknisi ini.');
    }
}
