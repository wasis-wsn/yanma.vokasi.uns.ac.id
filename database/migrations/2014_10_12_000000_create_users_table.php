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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('foto')->nullable();
            $table->string('google_id')->nullable();
            $table->unsignedBigInteger('prodi')->nullable()->default(1);
            $table->string('nim')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('no_wa')->nullable();
            $table->unsignedBigInteger('role')->nullable()->default(1);
            $table->unsignedBigInteger('pembina_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            // $table->softDeletes();

            // $table->foreign('prodi')->references('id')->on('ref_prodi')->onDelete('set null');
            // $table->foreign('role')->references('id')->on('roles')->onDelete('set null');
            // $table->foreign('pembina_id')->references('id')->on('pembina')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
