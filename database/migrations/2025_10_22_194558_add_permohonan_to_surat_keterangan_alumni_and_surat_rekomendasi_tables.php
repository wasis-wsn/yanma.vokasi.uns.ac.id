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
        Schema::table('surat_keterangan_alumni', function (Blueprint $table) {
            $table->text('permohonan')->nullable()->after('user_id');
        });

        Schema::table('surat_rekomendasi', function (Blueprint $table) {
            $table->text('permohonan')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keterangan_alumni', function (Blueprint $table) {
            $table->dropColumn('permohonan');
        });

        Schema::table('surat_rekomendasi', function (Blueprint $table) {
            $table->dropColumn('permohonan');
        });
    }
};
