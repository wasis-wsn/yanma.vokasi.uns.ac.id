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
        Schema::table('pemilihan_votes', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['candidate_id']);

            // Make candidate_id nullable
            $table->foreignId('candidate_id')->nullable()->change();

            // Re-add foreign key
            $table->foreign('candidate_id')
                  ->references('id')
                  ->on('pemilihan_candidates')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemilihan_votes', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['candidate_id']);

            // Make candidate_id not nullable
            $table->foreignId('candidate_id')->nullable(false)->change();

            // Re-add foreign key with cascade delete
            $table->foreign('candidate_id')
                  ->references('id')
                  ->on('pemilihan_candidates')
                  ->cascadeOnDelete();
        });
    }
};
