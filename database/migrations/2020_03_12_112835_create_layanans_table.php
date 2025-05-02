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
        Schema::create('layanans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('url_mhs')->nullable();
            $table->string('url_staff')->nullable();
            $table->unsignedBigInteger('kategori_layanan_id')->nullable();
            $table->enum('is_active', [0, 1])->default(0);
            $table->enum('is_default', [0, 1])->default(0);
            // $table->json('gate')->nullable();
            $table->integer('urutan')->nullable();
            $table->longText('keterangan')->nullable();
            // $table->string('semester')->nullable();
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->unsignedBigInteger('tahun_akademik_id')->nullable();
            $table->dateTime('open_datetime')->nullable();
            $table->dateTime('close_datetime')->nullable();
            $table->string('template_surat_hasil')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanans');
    }
};
