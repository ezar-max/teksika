<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workers') && !Schema::hasColumn('workers', 'tarif')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->decimal('tarif', 12, 2)->nullable()->after('skills');
            });
        }

        if (Schema::hasTable('request') && !Schema::hasColumn('request', 'id_worker_pilihan')) {
            Schema::table('request', function (Blueprint $table) {
                $table->unsignedBigInteger('id_worker_pilihan')->nullable()->after('id_worker');
                $table->foreign('id_worker_pilihan')->references('id')->on('workers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('request') && Schema::hasColumn('request', 'id_worker_pilihan')) {
            Schema::table('request', function (Blueprint $table) {
                $table->dropForeign(['id_worker_pilihan']);
                $table->dropColumn('id_worker_pilihan');
            });
        }

        if (Schema::hasTable('workers') && Schema::hasColumn('workers', 'tarif')) {
            Schema::table('workers', fn (Blueprint $table) => $table->dropColumn('tarif'));
        }
    }
};