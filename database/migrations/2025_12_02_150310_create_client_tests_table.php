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
        Schema::create('client_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->timestamp('spm_start_at')->nullable();
            $table->timestamp('spm_end_at')->nullable();

            $table->timestamp('papikostick_start_at')->nullable();
            $table->timestamp('papikostick_end_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Jika relasi ke users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_tests');
    }
};
