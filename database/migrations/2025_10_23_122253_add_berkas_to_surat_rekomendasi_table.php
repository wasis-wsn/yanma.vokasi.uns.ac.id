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
        Schema::table('surat_rekomendasi', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_rekomendasi', 'file_ijazah')) {
                $table->string('file_ijazah')->nullable()->after('surat_hasil');
            }

            if (!Schema::hasColumn('surat_rekomendasi', 'file_transkrip')) {
                $table->string('file_transkrip')->nullable()->after('file_ijazah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_rekomendasi', function (Blueprint $table) {
            if (Schema::hasColumn('surat_rekomendasi', 'file_ijazah')) {
                $table->dropColumn('file_ijazah');
            }

            if (Schema::hasColumn('surat_rekomendasi', 'file_transkrip')) {
                $table->dropColumn('file_transkrip');
            }
        });
    }
};
