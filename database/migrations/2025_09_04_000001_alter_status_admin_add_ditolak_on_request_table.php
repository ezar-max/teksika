<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan ENUM mencakup semua status yang digunakan aplikasi
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
        // Normalisasi data sebelum mempersempit ENUM agar aman
        DB::statement("UPDATE `request` SET `status_admin`='belum_diterima' WHERE `status_admin` NOT IN ('diterima','belum_diterima')");
        DB::statement("
            ALTER TABLE `request`
            MODIFY `status_admin` ENUM('diterima', 'belum_diterima')
            NOT NULL DEFAULT 'belum_diterima'
        ");
    }
};
