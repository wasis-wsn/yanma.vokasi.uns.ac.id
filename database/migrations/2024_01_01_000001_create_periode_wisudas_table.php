<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('periode_wisudas', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->integer('bulan'); // 1-12 for Jan-Dec
            $table->string('nama_bulan');
            $table->date('tanggal_wisuda')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['tahun', 'bulan']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('periode_wisudas');
    }
};
