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
    Schema::table('surat_tugas', function (Blueprint $table) {
        $table->integer('queue_number')->nullable()->after('status_id');
        $table->enum('queue_status', ['waiting', 'processed'])->default('waiting')->after('queue_number');
    });
}

public function down(): void
{
    Schema::table('surat_tugas', function (Blueprint $table) {
        $table->dropColumn(['queue_number', 'queue_status']);
    });
}

}; 