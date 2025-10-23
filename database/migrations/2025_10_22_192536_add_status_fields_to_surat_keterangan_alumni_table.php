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
            if (!Schema::hasColumn('surat_keterangan_alumni', 'status_id')) {
                $table->string('status_id')->default('1')->after('file');
            }

            if (!Schema::hasColumn('surat_keterangan_alumni', 'no_surat')) {
                $table->string('no_surat')->nullable()->after('status_id');
            }

            if (!Schema::hasColumn('surat_keterangan_alumni', 'catatan')) {
                $table->text('catatan')->nullable()->after('no_surat');
            }

            if (!Schema::hasColumn('surat_keterangan_alumni', 'tanggal_proses')) {
                $table->dateTime('tanggal_proses')->nullable()->after('catatan');
            }

            if (!Schema::hasColumn('surat_keterangan_alumni', 'surat_hasil')) {
                $table->string('surat_hasil')->nullable()->after('tanggal_proses');
            }

            if (!Schema::hasColumn('surat_keterangan_alumni', 'file_ijazah')) {
                $table->string('file_ijazah')->nullable()->after('surat_hasil');
            }

            if (!Schema::hasColumn('surat_keterangan_alumni', 'file_transkrip')) {
                $table->string('file_transkrip')->nullable()->after('file_ijazah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keterangan_alumni', function (Blueprint $table) {
            $table->dropColumn(['status_id', 'no_surat', 'catatan', 'tanggal_proses', 'surat_hasil']);
        });
    }
};
