<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            if (!Schema::hasColumn('workers', 'poin')) {
                $table->unsignedTinyInteger('poin')->default(0);
            }
        });

        Schema::table('worker_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('worker_offers', 'poin_snapshot')) {
                $table->unsignedTinyInteger('poin_snapshot')->default(0);
            }
            if (!Schema::hasColumn('worker_offers', 'biaya_additional_snapshot')) {
                $table->decimal('biaya_additional_snapshot', 12, 2)->default(0);
            }
        });

        Schema::table('request', function (Blueprint $table) {
            if (!Schema::hasColumn('request', 'biaya_additional_estimasi')) {
                $table->decimal('biaya_additional_estimasi', 12, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            if (Schema::hasColumn('workers', 'poin')) {
                $table->dropColumn('poin');
            }
        });

        Schema::table('worker_offers', function (Blueprint $table) {
            foreach (['poin_snapshot', 'biaya_additional_snapshot'] as $column) {
                if (Schema::hasColumn('worker_offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('request', function (Blueprint $table) {
            if (Schema::hasColumn('request', 'biaya_additional_estimasi')) {
                $table->dropColumn('biaya_additional_estimasi');
            }
        });
    }
};