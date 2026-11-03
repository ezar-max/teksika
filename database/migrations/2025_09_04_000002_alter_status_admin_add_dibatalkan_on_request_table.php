<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah nilai 'dibatalkan' ke enum status_admin
        DB::statement("
            ALTER TABLE `request`
            MODIFY `status_admin` ENUM('diterima', 'belum_diterima', 'ditolak', 'dibatalkan')
            NOT NULL DEFAULT 'belum_diterima'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Normalisasi data yang tidak masuk dalam set enum tujuan (hapus 'dibatalkan')
        DB::statement("UPDATE `request` SET `status_admin`='belum_diterima' WHERE `status_admin` NOT IN ('diterima','belum_diterima','ditolak')");
        // Kembalikan enum tanpa 'dibatalkan'
        DB::statement("
            ALTER TABLE `request`
            MODIFY `status_admin` ENUM('diterima', 'belum_diterima', 'ditolak')
            NOT NULL DEFAULT 'belum_diterima'
        ");
    }
};
