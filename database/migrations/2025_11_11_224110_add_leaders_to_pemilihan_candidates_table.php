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
        Schema::table('pemilihan_candidates', function (Blueprint $table) {
            $table->string('ketua_nama')->nullable()->after('name');
            $table->string('ketua_prodi')->nullable()->after('ketua_nama');
            $table->string('ketua_angkatan', 20)->nullable()->after('ketua_prodi');

            $table->string('wakil_nama')->nullable()->after('ketua_angkatan');
            $table->string('wakil_prodi')->nullable()->after('wakil_nama');
            $table->string('wakil_angkatan', 20)->nullable()->after('wakil_prodi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemilihan_candidates', function (Blueprint $table) {
            $table->dropColumn([
                'ketua_nama',
                'ketua_prodi',
                'ketua_angkatan',
                'wakil_nama',
                'wakil_prodi',
                'wakil_angkatan',
            ]);
        });
    }
};
