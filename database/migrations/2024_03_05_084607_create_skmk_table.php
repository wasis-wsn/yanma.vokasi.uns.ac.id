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
        Schema::create('skmk', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('status_id')->nullable();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_kemahasiswaans')->onDelete('set null');
            $table->foreignId('tahun_akademik_id')->nullable()->references('id')->on('tahun_akademiks')->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->references('id')->on('semesters')->onDelete('set null');
            $table->string('semester_romawi')->nullable();
            // $table->string('tahun_akademik')->nullable();
            $table->string('nama_ortu')->nullable();
            $table->string('nip_ortu')->nullable();
            $table->string('pangkat_ortu')->nullable();
            $table->string('instansi_ortu')->nullable();
            $table->string('alamat_instansi')->nullable();
            $table->string('catatan')->nullable();
            $table->string('file')->nullable();
            $table->string('surat_hasil')->nullable();
            $table->string('no_surat')->nullable();
            $table->dateTime('tanggal_proses')->nullable();
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('status_id')->references('id')->on('status_kemahasiswaans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skmk');
    }
};
