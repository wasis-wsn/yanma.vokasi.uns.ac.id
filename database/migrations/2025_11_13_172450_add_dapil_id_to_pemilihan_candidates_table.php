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
            $table->foreignId('dapil_id')->nullable()->after('pemilihan_id')->constrained('dapils')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemilihan_candidates', function (Blueprint $table) {
            $table->dropForeign(['dapil_id']);
            $table->dropColumn('dapil_id');
        });
    }
};
