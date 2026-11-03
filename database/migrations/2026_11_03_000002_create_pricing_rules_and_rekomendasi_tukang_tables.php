<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table pricing_rules
        if (!Schema::hasTable('pricing_rules')) {
            Schema::create('pricing_rules', function (Blueprint $table) {
                $table->id();
                $table->string('key', 50)->unique();
                $table->string('value', 255);
                $table->string('label', 100);
                $table->string('tipe', 20)->default('number'); // number, percent, text
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 2. Table rekomendasi_tukang
        if (!Schema::hasTable('rekomendasi_tukang')) {
            Schema::create('rekomendasi_tukang', function (Blueprint $table) {
                $table->id();
                $table->integer('id_request');
                $table->unsignedBigInteger('id_worker');
                $table->decimal('jarak_km', 8, 2)->default(0);
                $table->decimal('skor_jarak', 6, 4)->default(0);
                $table->decimal('skor_rating', 6, 4)->default(0);
                $table->decimal('skor_pengalaman', 6, 4)->default(0);
                $table->decimal('skor_beban', 6, 4)->default(0);
                $table->decimal('skor_total', 6, 4)->default(0);
                $table->integer('ranking')->default(1);
                $table->timestamps();

                $table->index('id_request');
                $table->index('id_worker');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_tukang');
        Schema::dropIfExists('pricing_rules');
    }
};

