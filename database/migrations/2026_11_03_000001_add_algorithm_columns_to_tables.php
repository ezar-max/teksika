<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table `request` additions & status fix
        if (Schema::hasTable('request')) {
            Schema::table('request', function (Blueprint $table) {
                if (!Schema::hasColumn('request', 'latitude')) {
                    $table->decimal('latitude', 10, 7)->nullable()->after('lokasi');
                }
                if (!Schema::hasColumn('request', 'longitude')) {
                    $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
                }
                if (!Schema::hasColumn('request', 'kompleksitas')) {
                    $table->enum('kompleksitas', ['rendah', 'sedang', 'tinggi'])->default('sedang')->after('urgensi');
                }
                if (!Schema::hasColumn('request', 'jarak_km')) {
                    $table->decimal('jarak_km', 8, 2)->nullable()->after('kompleksitas');
                }
                if (!Schema::hasColumn('request', 'harga_layanan')) {
                    $table->decimal('harga_layanan', 12, 2)->nullable()->after('jarak_km');
                }
                if (!Schema::hasColumn('request', 'faktor_urgensi')) {
                    $table->decimal('faktor_urgensi', 4, 2)->nullable()->after('harga_layanan');
                }
                if (!Schema::hasColumn('request', 'biaya_jarak')) {
                    $table->decimal('biaya_jarak', 12, 2)->nullable()->after('faktor_urgensi');
                }
                if (!Schema::hasColumn('request', 'estimasi_total')) {
                    $table->decimal('estimasi_total', 12, 2)->nullable()->after('biaya_jarak');
                }
                if (!Schema::hasColumn('request', 'estimasi_durasi_menit')) {
                    $table->integer('estimasi_durasi_menit')->nullable()->after('estimasi_total');
                }
                if (!Schema::hasColumn('request', 'skor_tukang')) {
                    $table->decimal('skor_tukang', 5, 3)->nullable()->after('estimasi_durasi_menit');
                }
            });

            // Modify status enum column to standard values: 'menunggu', 'dikonfirmasi', 'dikerjakan', 'selesai', 'dibatalkan'
            DB::statement("ALTER TABLE `request` MODIFY COLUMN `status` ENUM('menunggu', 'dikonfirmasi', 'dikerjakan', 'selesai', 'dibatalkan') DEFAULT 'menunggu'");
            DB::statement("UPDATE `request` SET `status` = 'menunggu' WHERE `status` IS NULL OR `status` NOT IN ('menunggu', 'dikonfirmasi', 'dikerjakan', 'selesai', 'dibatalkan')");
        }

        // 2. Table `services` additions
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (!Schema::hasColumn('services', 'durasi_dasar_menit')) {
                    $table->integer('durasi_dasar_menit')->default(60)->after('harga_max');
                }
            });
        }

        // 3. Table `workers` additions
        if (Schema::hasTable('workers')) {
            Schema::table('workers', function (Blueprint $table) {
                if (!Schema::hasColumn('workers', 'latitude')) {
                    $table->decimal('latitude', 10, 7)->nullable()->after('alamat');
                }
                if (!Schema::hasColumn('workers', 'longitude')) {
                    $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
                }
                if (!Schema::hasColumn('workers', 'rating')) {
                    $table->decimal('rating', 3, 2)->default(5.00)->after('pengalaman');
                }
            });
        }

        // 4. Table `users` additions
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'alamat')) {
                    $table->string('alamat', 255)->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'latitude')) {
                    $table->decimal('latitude', 10, 7)->nullable()->after('alamat');
                }
                if (!Schema::hasColumn('users', 'longitude')) {
                    $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('request')) {
            Schema::table('request', function (Blueprint $table) {
                $cols = ['latitude', 'longitude', 'kompleksitas', 'jarak_km', 'harga_layanan', 'faktor_urgensi', 'biaya_jarak', 'estimasi_total', 'estimasi_durasi_menit', 'skor_tukang'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('request', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (Schema::hasColumn('services', 'durasi_dasar_menit')) {
                    $table->dropColumn('durasi_dasar_menit');
                }
            });
        }

        if (Schema::hasTable('workers')) {
            Schema::table('workers', function (Blueprint $table) {
                $cols = ['latitude', 'longitude', 'rating'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('workers', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $cols = ['alamat', 'latitude', 'longitude'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('users', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

