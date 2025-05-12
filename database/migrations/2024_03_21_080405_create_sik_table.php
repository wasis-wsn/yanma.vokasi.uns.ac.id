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
        Schema::create('sik', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('ormawa_id');
            // $table->unsignedBigInteger('status_id')->default(1);
            $table->foreignId('ormawa_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('ketua_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_kemahasiswaans')->onDelete('set null');
            $table->string('catatan')->nullable();
            $table->string('file')->nullable();
            $table->string('surat_hasil')->nullable();
            $table->string('no_surat')->nullable();
            $table->dateTime('tanggal_proses')->nullable();
            $table->string('nama_kegiatan')->nullable();
            // $table->unsignedBigInteger('ketua_id');
            $table->string('no_surat_ormawa')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->enum('is_dana', [0, 1])->default(0);
            $table->date('tanggal_lpj')->nullable();
            $table->dateTime('mulai_kegiatan')->nullable();
            $table->dateTime('selesai_kegiatan')->nullable();
            $table->string('tempat')->nullable();
            $table->timestamps();

            // $table->foreign('ormawa_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('ketua_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('status_id')->references('id')->on('status_kemahasiswaans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sik');
    }
};
