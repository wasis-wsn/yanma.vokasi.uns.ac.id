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
        Schema::create('legalisir', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('status_id')->default(1);
            $table->string('name')->nullable();
            $table->string('nim')->nullable();
            // $table->unsignedBigInteger('prodi_id')->default(1);
            $table->foreignId('prodi_id')->nullable()->references('id')->on('ref_prodi')->onDelete('set null');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_legalisirs')->onDelete('set null');
            $table->string('catatan')->nullable();
            $table->dateTime('tanggal_proses')->nullable();
            $table->dateTime('tanggal_expired')->nullable();
            $table->dateTime('tanggal_ambil')->nullable();
            $table->string('legalisir')->nullable();
            $table->unsignedInteger('jumlah')->nullable();
            $table->string('keperluan')->nullable();
            $table->string('no_wa')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->timestamps();

            // $table->foreign('prodi_id')->references('id')->on('ref_prodi')->onDelete('set null');
            // $table->foreign('status_id')->references('id')->on('status_legalisirs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalisir');
    }
};
