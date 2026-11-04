<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request', function (Blueprint $table) {
            if (!Schema::hasColumn('request', 'harga_dasar_estimasi')) {
                $table->decimal('harga_dasar_estimasi', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('request', 'biaya_kualifikasi_estimasi')) {
                $table->decimal('biaya_kualifikasi_estimasi', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('request', 'rating_estimasi')) {
                $table->decimal('rating_estimasi', 3, 2)->nullable();
            }
            if (!Schema::hasColumn('request', 'pengalaman_estimasi')) {
                $table->unsignedInteger('pengalaman_estimasi')->nullable();
            }
        });

        Schema::table('worker_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('worker_offers', 'biaya_kualifikasi_snapshot')) {
                $table->decimal('biaya_kualifikasi_snapshot', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('request', function (Blueprint $table) {
            foreach (['harga_dasar_estimasi', 'biaya_kualifikasi_estimasi', 'rating_estimasi', 'pengalaman_estimasi'] as $column) {
                if (Schema::hasColumn('request', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('worker_offers', function (Blueprint $table) {
            if (Schema::hasColumn('worker_offers', 'biaya_kualifikasi_snapshot')) {
                $table->dropColumn('biaya_kualifikasi_snapshot');
            }
        });
    }
};
