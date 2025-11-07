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
        Schema::table('verifikasi_wisuda', function (Blueprint $table) {
            if (!Schema::hasColumn('verifikasi_wisuda', 'periode_wisuda')) {
                $table->string('periode_wisuda')->nullable()->after('pin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifikasi_wisuda', function (Blueprint $table) {
            if (Schema::hasColumn('verifikasi_wisuda', 'periode_wisuda')) {
                $table->dropColumn('periode_wisuda');
            }
        });
    }
};
