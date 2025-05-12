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
        Schema::create('akreditasis', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('prodi_id');
            $table->foreignId('prodi_id')->references('id')->on('ref_prodi')->onDelete('cascade');
            $table->string('tahun');
            $table->string('file');
            $table->timestamps();
            // $table->foreign('prodi_id')->references('id')->on('ref_prodi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akreditasis');
    }
};
