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
        Schema::create('diluar_jadwals', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('status_id')->default(1);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_heregistrasis')->onDelete('set null');
            $table->string('catatan')->nullable();
            $table->string('no_surat')->nullable();
            $table->string('semester_romawi')->nullable();
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->unsignedBigInteger('tahun_akademik_id')->nullable();
            $table->longText('alasan')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('surat_permohonan')->nullable();
            $table->string('bukti_bayar_ukt')->nullable();
            $table->string('izin_cuti')->nullable();
            $table->dateTime('tanggal_proses')->nullable();
            $table->dateTime('tanggal_ambil')->nullable();
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('status_id')->references('id')->on('status_heregistrasis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diluar_jadwals');
    }
};
