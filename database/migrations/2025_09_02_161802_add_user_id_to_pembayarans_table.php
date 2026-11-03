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
        if (!Schema::hasColumn('pembayarans', 'user_id')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->after('id');
                // Tambahkan FK bila kolom baru dibuat
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pembayarans', 'user_id')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                // Hapus FK jika ada, lalu kolom
                try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
                $table->dropColumn('user_id');
            });
        }
    }
};
