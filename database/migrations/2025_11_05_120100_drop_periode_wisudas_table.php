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
        Schema::dropIfExists('periode_wisudas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('periode_wisudas', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->integer('bulan');
            $table->string('nama_bulan');
            $table->date('tanggal_wisuda')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->unique(['tahun', 'bulan']);
        });
    }
};
