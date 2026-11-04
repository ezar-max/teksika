<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('worker_offers', 'biaya_jasa_snapshot')) {
                $table->decimal('biaya_jasa_snapshot', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('worker_offers', 'biaya_material_snapshot')) {
                $table->decimal('biaya_material_snapshot', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('worker_offers', function (Blueprint $table) {
            foreach (['biaya_jasa_snapshot', 'biaya_material_snapshot'] as $column) {
                if (Schema::hasColumn('worker_offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};