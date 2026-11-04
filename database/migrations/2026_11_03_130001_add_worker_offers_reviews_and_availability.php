<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            if (!Schema::hasColumn('workers', 'foto')) {
                $table->string('foto')->nullable();
            }
            if (!Schema::hasColumn('workers', 'status_ketersediaan')) {
                $table->string('status_ketersediaan', 30)->default('siap');
            }
        });

        Schema::table('request', function (Blueprint $table) {
            if (!Schema::hasColumn('request', 'id_penawaran_terpilih')) {
                $table->unsignedBigInteger('id_penawaran_terpilih')->nullable();
            }
        });

        if (!Schema::hasTable('worker_offers')) {
            Schema::create('worker_offers', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('id_request');
                $table->unsignedBigInteger('id_worker');
                $table->decimal('biaya_jasa_material', 12, 2);
                $table->decimal('biaya_transportasi', 12, 2);
                $table->decimal('total_estimasi', 12, 2);
                $table->decimal('jarak_km', 8, 2);
                $table->unsignedInteger('estimasi_durasi_menit')->nullable();
                $table->decimal('rating_snapshot', 3, 2);
                $table->unsignedInteger('pengalaman_snapshot');
                $table->unsignedInteger('jumlah_ulasan_snapshot')->default(0);
                $table->string('keahlian_snapshot', 255);
                $table->decimal('faktor_harga_snapshot', 4, 3)->default(1);
                $table->string('status', 20)->default('ditawarkan');
                $table->timestamp('dipilih_at')->nullable();
                $table->timestamp('ditugaskan_at')->nullable();
                $table->timestamps();

                $table->unique(['id_request', 'id_worker']);
                $table->foreign('id_request')->references('id_request')->on('request')->cascadeOnDelete();
                $table->foreign('id_worker')->references('id')->on('workers')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('worker_reviews')) {
            Schema::create('worker_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('id_request')->unique();
                $table->unsignedBigInteger('id_worker');
                $table->unsignedBigInteger('id_user');
                $table->unsignedTinyInteger('rating');
                $table->text('ulasan')->nullable();
                $table->timestamps();

                $table->foreign('id_request')->references('id_request')->on('request')->cascadeOnDelete();
                $table->foreign('id_worker')->references('id')->on('workers')->cascadeOnDelete();
                $table->foreign('id_user')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_reviews');
        Schema::dropIfExists('worker_offers');

        if (Schema::hasColumn('request', 'id_penawaran_terpilih')) {
            Schema::table('request', fn (Blueprint $table) => $table->dropColumn('id_penawaran_terpilih'));
        }

        Schema::table('workers', function (Blueprint $table) {
            foreach (['foto', 'status_ketersediaan'] as $column) {
                if (Schema::hasColumn('workers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};