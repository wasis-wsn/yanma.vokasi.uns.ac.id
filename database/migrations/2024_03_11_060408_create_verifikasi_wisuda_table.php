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
        Schema::create('verifikasi_wisuda', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('status_id')->default(1);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_wisudas')->onDelete('set null');
            $table->string('catatan')->nullable();
            $table->dateTime('tanggal_proses')->nullable();
            $table->string('no_seri_ijazah')->nullable();
            $table->string('file')->nullable();
            $table->string('periode_wisuda')->nullable();
            $table->string('kode_akses')->nullable();
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('status_id')->references('id')->on('status_wisudas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_wisuda');
    }
};
