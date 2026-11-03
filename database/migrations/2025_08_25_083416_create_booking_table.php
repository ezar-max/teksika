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
        Schema::create('booking', function (Blueprint $table) {
            $table->increments('id_booking');
            $table->unsignedBigInteger('id_user');
            $table->string('layanan', 100);
            $table->string('lokasi', 100);
            $table->dateTime('tanggal_booking');
            $table->decimal('harga', 10, 2);
            $table->enum('status', ['Pending', 'selesai', 'batal']);
            $table->timestamps();

            // Foreign key ke tabel users
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
