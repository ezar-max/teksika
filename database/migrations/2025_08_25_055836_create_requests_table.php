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
        Schema::create('request', function (Blueprint $table) {
            $table->increments('id_request');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_worker')->nullable(); // worker yang dipilih
            $table->string('layanan', 50);
            $table->string('lokasi', 100);
            $table->enum('urgensi', ['rendah', 'sedang', 'tinggi']);
            $table->text('detail');
            $table->enum('status', ['rendah', 'sedang', 'tinggi']);
            $table->enum('status_admin', ['diterima', 'belum_diterima'])->default('belum_diterima'); // status admin
            $table->dateTime('tanggal');
            $table->string('foto')->nullable();
            $table->decimal('jumlah_pembayaran', 12, 2)->nullable();
            $table->boolean('bayar_sekarang')->default(0);
            $table->string('metode_pembayaran', 20)->nullable();
            $table->timestamps();

            // Foreign key ke tabel users
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_worker')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request');
    }
};
