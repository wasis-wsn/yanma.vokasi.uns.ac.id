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
        Schema::table('pemilihan_candidates', function (Blueprint $table) {
            $table->foreignId('prodi_id')
                ->nullable()
                ->after('pemilihan_id')
                ->constrained('ref_prodi')
                ->nullOnDelete();
        });

        Schema::create('pemilihan_candidate_dapils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('pemilihan_candidates')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('ref_prodi')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['candidate_id', 'prodi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemilihan_candidate_dapils');

        Schema::table('pemilihan_candidates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prodi_id');
        });
    }
};
