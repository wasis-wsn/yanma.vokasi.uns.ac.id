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
        Schema::create('skpi', function (Blueprint $table) {
            $table->id();
            $table->string('no_skpi')->nullable();
            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('status_id')->default(1);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->references('id')->on('status_transkrips')->onDelete('set null');
            $table->dateTime('tanggal_ambil')->nullable();
            $table->string('catatan')->nullable();
            $table->string('periode_wisuda')->nullable();
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('status_id')->references('id')->on('status_transkrips')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skpi');
    }
};
