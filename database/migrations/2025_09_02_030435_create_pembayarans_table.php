<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('pembayarans', function (Blueprint $table) {
        $table->id();
        $table->string('kode_pembayaran')->unique();
        $table->decimal('jumlah', 10, 2);
        $table->string('metode_pembayaran'); // transfer, cash, kartu_kredit
        $table->string('status')->default('pending'); // pending, success, failed
        $table->string('nama_pembayar');
        $table->string('email_pembayar')->nullable();
        $table->text('keterangan')->nullable();
        $table->unsignedBigInteger('user_id'); // Tambahkan ini!
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
