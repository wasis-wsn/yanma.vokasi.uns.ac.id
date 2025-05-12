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
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('status_id')->default(1);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_kemahasiswaans')->onDelete('set null');
            $table->string('catatan')->nullable();
            $table->string('file')->nullable();
            $table->string('surat_hasil')->nullable();
            $table->string('no_surat')->nullable();
            $table->dateTime('tanggal_proses')->nullable();
            $table->string('nama_kegiatan')->nullable();
            $table->dateTime('mulai_kegiatan')->nullable();
            $table->dateTime('selesai_kegiatan')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('tempat')->nullable();
            $table->string('delegasi')->nullable();
            $table->integer('jumlah_peserta')->nullable();
            $table->string('dospem')->nullable();
            $table->string('nip_dospem')->nullable();
            $table->string('nidn_dospem')->nullable();
            $table->string('unit_dospem')->nullable();
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
        Schema::dropIfExists('surat_tugas');
    }
};
