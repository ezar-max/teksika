<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request', function (Blueprint $table) {
            if (!Schema::hasColumn('request', 'biaya_urgensi_estimasi')) {
                $table->decimal('biaya_urgensi_estimasi', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('request', 'biaya_kerusakan_estimasi')) {
                $table->decimal('biaya_kerusakan_estimasi', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('request', 'id_worker_estimasi')) {
                $table->unsignedBigInteger('id_worker_estimasi')->nullable();
                $table->foreign('id_worker_estimasi')->references('id')->on('workers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('request', function (Blueprint $table) {
            if (Schema::hasColumn('request', 'id_worker_estimasi')) {
                $table->dropForeign(['id_worker_estimasi']);
                $table->dropColumn('id_worker_estimasi');
            }
            foreach (['biaya_urgensi_estimasi', 'biaya_kerusakan_estimasi'] as $column) {
                if (Schema::hasColumn('request', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
