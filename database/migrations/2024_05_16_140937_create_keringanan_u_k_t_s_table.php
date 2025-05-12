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
        Schema::create('keringanan_ukts', function (Blueprint $table) {
            $table->id();
            $table->string('jenis')->nullable();
            $table->longText('keterangan')->nullable();
            $table->longText('persyaratan')->nullable();
            $table->string('pengajuan')->nullable();
            $table->string('verif_fakultas')->nullable();
            $table->string('verif_univ')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keringanan_ukts');
    }
};
