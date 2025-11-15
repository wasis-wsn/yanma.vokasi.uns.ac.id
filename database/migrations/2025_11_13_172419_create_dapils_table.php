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
        Schema::create('dapils', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Dapil 1, Dapil 2, Dapil 3
            $table->timestamps();
        });

        // Create pivot table for dapil and prodi relationship
        Schema::create('dapil_prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dapil_id')->constrained('dapils')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('ref_prodi')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['dapil_id', 'prodi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dapil_prodi');
        Schema::dropIfExists('dapils');
    }
};
