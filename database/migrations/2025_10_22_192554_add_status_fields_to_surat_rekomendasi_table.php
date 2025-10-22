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
            $table->string('status_id')->default('1')->after('file');
            $table->string('no_surat')->nullable()->after('status_id');
            $table->text('catatan')->nullable()->after('no_surat');
            $table->dateTime('tanggal_proses')->nullable()->after('catatan');
            $table->string('surat_hasil')->nullable()->after('tanggal_proses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_rekomendasi', function (Blueprint $table) {
            $table->dropColumn(['status_id', 'no_surat', 'catatan', 'tanggal_proses', 'surat_hasil']);
        });
    }
};
