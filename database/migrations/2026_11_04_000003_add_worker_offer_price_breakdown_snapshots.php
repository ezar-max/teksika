<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('worker_offers', 'harga_dasar_snapshot')) {
                $table->decimal('harga_dasar_snapshot', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('worker_offers', 'biaya_urgensi_snapshot')) {
                $table->decimal('biaya_urgensi_snapshot', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('worker_offers', 'biaya_kerusakan_snapshot')) {
                $table->decimal('biaya_kerusakan_snapshot', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('worker_offers', function (Blueprint $table) {
            foreach (['harga_dasar_snapshot', 'biaya_urgensi_snapshot', 'biaya_kerusakan_snapshot'] as $column) {
                if (Schema::hasColumn('worker_offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
