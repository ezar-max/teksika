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
        Schema::table('request', function (Blueprint $table) {
            $table->string('kode_booking', 20)->unique()->nullable()->after('id_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request', function (Blueprint $table) {
            $table->dropColumn('kode_booking');
        });
    }
};
