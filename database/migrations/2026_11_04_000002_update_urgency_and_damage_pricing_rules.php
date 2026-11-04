<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pricing_rules')) {
            return;
        }

        $rules = [
            'faktor_urgensi_rendah' => ['1.00', 'Faktor Urgensi Normal/Rendah', 'Pengali harga untuk urgensi normal (tarif standar).'],
            'faktor_urgensi_sedang' => ['1.20', 'Faktor Urgensi Mendesak/Sedang', 'Pengali harga untuk urgensi mendesak (+20%).'],
            'faktor_urgensi_tinggi' => ['1.40', 'Faktor Urgensi Darurat/Tinggi', 'Pengali harga untuk urgensi darurat (+40%).'],
            'faktor_kerusakan_ringan' => ['1.00', 'Faktor Kerusakan Ringan', 'Tidak ada tambahan harga untuk kerusakan ringan.'],
            'faktor_kerusakan_sedang' => ['1.05', 'Faktor Kerusakan Sedang', 'Tambahan harga 5% untuk kerusakan sedang.'],
            'faktor_kerusakan_berat' => ['1.10', 'Faktor Kerusakan Berat', 'Tambahan harga 10% untuk kerusakan berat.'],
        ];

        foreach ($rules as $key => [$value, $label, $description]) {
            DB::table('pricing_rules')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'label' => $label, 'tipe' => 'number', 'keterangan' => $description, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pricing_rules')) {
            DB::table('pricing_rules')->whereIn('key', [
                'faktor_kerusakan_ringan',
                'faktor_kerusakan_sedang',
                'faktor_kerusakan_berat',
            ])->delete();
        }
    }
};