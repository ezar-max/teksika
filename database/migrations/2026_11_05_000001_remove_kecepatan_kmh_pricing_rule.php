<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pricing_rules')) {
            DB::table('pricing_rules')->where('key', 'kecepatan_kmh')->delete();
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pricing_rules')) {
            DB::table('pricing_rules')->updateOrInsert(
                ['key' => 'kecepatan_kmh'],
                [
                    'value' => '30',
                    'label' => 'Estimasi Kecepatan Tempuh (km/jam)',
                    'tipe' => 'number',
                    'keterangan' => 'Dipakai untuk estimasi waktu tempuh tukang menuju lokasi user (2 menit/km pada 30 km/jam).',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
};