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
        Schema::create('perpanjangan_studis', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('status_id')->default(1);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_heregistrasis')->onDelete('set null');
            $table->string('catatan')->nullable();
            $table->string('no_surat')->nullable();
            $table->string('file')->nullable();
            // $table->string('semester')->nullable();
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->unsignedBigInteger('tahun_akademik_id')->nullable();
            $table->integer('perpanjangan_ke')->nullable();
            $table->integer('queue_number')->nullable(); // Hapus after()
            $table->boolean('queue_status')->default(false); // Hapus after()
            $table->dateTime('tanggal_proses')->nullable();
            $table->dateTime('tanggal_ambil')->nullable();
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('status_id')->references('id')->on('status_heregistrasis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perpanjangan_studis', function (Blueprint $table) {
            $table->dropColumn(['queue_number', 'queue_status']);
        });
    }
};
