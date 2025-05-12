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
        Schema::create('lpjs', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('sik_id')->nullable();
            // $table->unsignedBigInteger('status_id')->default(1);
            // $table->unsignedBigInteger('surat_tugas_id')->nullable();
            $table->foreignId('sik_id')->nullable()->references('id')->on('sik')->onDelete('cascade');
            $table->foreignId('surat_tugas_id')->nullable()->references('id')->on('surat_tugas')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_lpjs')->onDelete('set null');
            $table->string('catatan')->nullable();
            $table->string('file')->nullable();
            $table->datetime('tanggal_proses')->nullable();
            $table->timestamps();

            // $table->foreign('sik_id')->references('id')->on('sik')->onDelete('cascade');
            // $table->foreign('surat_tugas_id')->references('id')->on('surat_tugas')->onDelete('cascade');
            // $table->foreign('status_id')->references('id')->on('status_lpjs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpjs');
    }
};
