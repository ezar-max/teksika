<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request', function (Blueprint $table) {
            if (!Schema::hasColumn('request', 'poin_estimasi')) {
                $table->unsignedTinyInteger('poin_estimasi')->nullable();
            }
            foreach (['biaya_rating_estimasi', 'biaya_pengalaman_estimasi'] as $column) {
                if (!Schema::hasColumn('request', $column)) {
                    $table->decimal($column, 12, 2)->nullable();
                }
            }
            foreach (['pengalaman_persen_estimasi', 'poin_persen_estimasi'] as $column) {
                if (!Schema::hasColumn('request', $column)) {
                    $table->decimal($column, 8, 3)->nullable();
                }
            }
        });

        Schema::table('worker_offers', function (Blueprint $table) {
            foreach (['biaya_rating_snapshot', 'biaya_pengalaman_snapshot'] as $column) {
                if (!Schema::hasColumn('worker_offers', $column)) {
                    $table->decimal($column, 12, 2)->nullable();
                }
            }
            foreach (['pengalaman_persen_snapshot', 'poin_persen_snapshot'] as $column) {
                if (!Schema::hasColumn('worker_offers', $column)) {
                    $table->decimal($column, 8, 3)->nullable();
                }
            }
        });

        if (Schema::hasTable('pricing_rules')) {
            $now = now();
            foreach ([
                ['key' => 'poin_tarif_persen', 'value' => '2.00', 'label' => 'Biaya Poin (% tarif dasar)', 'tipe' => 'number', 'keterangan' => 'Persentase tarif dasar yang dikalikan dengan poin/100.'],
                ['key' => 'pengalaman_per_tahun_persen', 'value' => '1.00', 'label' => 'Tambahan Pengalaman (% per tahun)', 'tipe' => 'number', 'keterangan' => 'Persentase tambahan tarif dasar untuk setiap tahun pengalaman.'],
                ['key' => 'pengalaman_batas_persen', 'value' => '25.00', 'label' => 'Batas Tambahan Pengalaman (%)', 'tipe' => 'number', 'keterangan' => 'Batas maksimum persentase tambahan tarif dasar dari pengalaman.'],
            ] as $rule) {
                DB::table('pricing_rules')->insertOrIgnore($rule + [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('request', function (Blueprint $table) {
            foreach (['poin_estimasi', 'biaya_rating_estimasi', 'biaya_pengalaman_estimasi', 'pengalaman_persen_estimasi', 'poin_persen_estimasi'] as $column) {
                if (Schema::hasColumn('request', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('worker_offers', function (Blueprint $table) {
            foreach (['biaya_rating_snapshot', 'biaya_pengalaman_snapshot', 'pengalaman_persen_snapshot', 'poin_persen_snapshot'] as $column) {
                if (Schema::hasColumn('worker_offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        DB::table('pricing_rules')->whereIn('key', [
            'poin_tarif_persen',
            'pengalaman_per_tahun_persen',
            'pengalaman_batas_persen',
        ])->delete();
    }
};
