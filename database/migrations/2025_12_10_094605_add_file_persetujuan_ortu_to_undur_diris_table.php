<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('undur_diris', function (Blueprint $table) {
            $table->string('file_persetujuan_ortu')->nullable()->after('file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('undur_diris', function (Blueprint $table) {
            $table->dropColumn('file_persetujuan_ortu');
        });
    }
};
