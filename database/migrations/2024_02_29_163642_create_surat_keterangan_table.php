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
        Schema::create('surat_keterangan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('status_kemahasiswaans')
                ->onDelete('set null');

            $table->foreignId('tahun_akademik_id')
                ->nullable()
                ->constrained('tahun_akademiks')
                ->onDelete('set null');

            $table->foreignId('semester_id')
                ->nullable()
                ->constrained('semesters')
                ->onDelete('set null');

            // kolom dari migration kedua sudah dimasukkan di sini
            $table->integer('queue_number')->nullable()->after('status_id');
            $table->enum('queue_status', ['waiting', 'processed'])->default('waiting')->after('queue_number');

            $table->string('keperluan')->nullable();
            $table->string('file')->nullable();
            $table->string('surat_hasil')->nullable();
            $table->string('catatan')->nullable();
            $table->string('no_surat')->nullable();
            $table->dateTime('tanggal_proses')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keterangan');
    }
};
