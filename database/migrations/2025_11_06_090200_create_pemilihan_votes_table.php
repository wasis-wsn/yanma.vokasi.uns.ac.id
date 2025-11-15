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
        Schema::create('pemilihan_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemilihan_id')->constrained('pemilihans')->cascadeOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained('pemilihan_candidates')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('voted_at')->useCurrent();
            $table->timestamps();

            $table->unique(['pemilihan_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemilihan_votes');
    }
};
