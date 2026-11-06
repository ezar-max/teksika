<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OBSOLETE_RULES = [
        'poin_tarif_persen',
        'pengalaman_per_tahun_persen',
        'pengalaman_batas_persen',
    ];

    public function up(): void
    {
        if (Schema::hasTable('pricing_rules')) {
            DB::table('pricing_rules')->whereIn('key', self::OBSOLETE_RULES)->delete();
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('pricing_rules')) {
            return;
        }

        $now = now();
        foreach ([
            ['key' => 'poin_tarif_persen', 'value' => '2.00', 'label' => 'Biaya Poin (% tarif dasar)'],
            ['key' => 'pengalaman_per_tahun_persen', 'value' => '1.00', 'label' => 'Tambahan Pengalaman (% per tahun)'],
            ['key' => 'pengalaman_batas_persen', 'value' => '25.00', 'label' => 'Batas Tambahan Pengalaman (%)'],
        ] as $rule) {
            DB::table('pricing_rules')->insertOrIgnore($rule + [
                'tipe' => 'number',
                'keterangan' => 'Aturan tarif tukang versi sebelumnya.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
