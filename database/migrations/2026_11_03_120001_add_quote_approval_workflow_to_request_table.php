<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request', function (Blueprint $table) {
            if (!Schema::hasColumn('request', 'tingkat_kerusakan')) {
                $table->string('tingkat_kerusakan', 30)->nullable();
            }
            if (!Schema::hasColumn('request', 'penawaran_keahlian')) {
                $table->string('penawaran_keahlian', 150)->nullable();
            }
            if (!Schema::hasColumn('request', 'biaya_material')) {
                $table->decimal('biaya_material', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('request', 'penawaran_catatan')) {
                $table->text('penawaran_catatan')->nullable();
            }
            if (!Schema::hasColumn('request', 'durasi_perjalanan_menit')) {
                $table->unsignedInteger('durasi_perjalanan_menit')->nullable();
            }
            if (!Schema::hasColumn('request', 'penawaran_dikirim_at')) {
                $table->timestamp('penawaran_dikirim_at')->nullable();
            }
            if (!Schema::hasColumn('request', 'persetujuan_user_at')) {
                $table->timestamp('persetujuan_user_at')->nullable();
            }
            if (!Schema::hasColumn('request', 'verifikasi_admin_at')) {
                $table->timestamp('verifikasi_admin_at')->nullable();
            }
        });

        DB::statement("ALTER TABLE `request` MODIFY COLUMN `status` ENUM('menunggu', 'menunggu_persetujuan', 'persetujuan_dikirim', 'dikonfirmasi', 'dikerjakan', 'selesai', 'dibatalkan', 'ditolak') NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("UPDATE `request` SET `status` = 'menunggu' WHERE `status` IN ('menunggu_persetujuan', 'persetujuan_dikirim')");
        DB::statement("UPDATE `request` SET `status` = 'dibatalkan' WHERE `status` = 'ditolak'");
        DB::statement("ALTER TABLE `request` MODIFY COLUMN `status` ENUM('menunggu', 'dikonfirmasi', 'dikerjakan', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'menunggu'");

        Schema::table('request', function (Blueprint $table) {
            foreach ([
                'tingkat_kerusakan',
                'penawaran_keahlian',
                'biaya_material',
                'penawaran_catatan',
                'durasi_perjalanan_menit',
                'penawaran_dikirim_at',
                'persetujuan_user_at',
                'verifikasi_admin_at',
            ] as $column) {
                if (Schema::hasColumn('request', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};