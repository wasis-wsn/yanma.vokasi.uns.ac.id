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
        Schema::create('pembina', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('nip')->nullable();
            $table->string('nidn')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('unit_id')->references('id')->on('ref_prodi')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembina');
    }
};
