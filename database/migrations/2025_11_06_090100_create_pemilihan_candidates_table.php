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
        Schema::create('pemilihan_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemilihan_id')->constrained('pemilihans')->cascadeOnDelete();
            $table->foreignId('prodi_id')->nullable()->constrained('ref_prodi')->nullOnDelete();
            $table->unsignedTinyInteger('nomor_urut')->default(1);
            $table->string('name');
            $table->string('ketua_nama')->nullable();
            $table->string('ketua_prodi')->nullable();
            $table->string('ketua_angkatan', 20)->nullable();
            $table->string('wakil_nama')->nullable();
            $table->string('wakil_prodi')->nullable();
            $table->string('wakil_angkatan', 20)->nullable();
            $table->string('foto')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['pemilihan_id', 'nomor_urut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemilihan_candidates');
    }
};
