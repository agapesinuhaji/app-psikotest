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
        Schema::create('spm_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 5 domain SPM
            $table->integer('logical_thinking')->default(0);     // A
            $table->integer('analytical_power')->default(0);     // B
            $table->integer('numerical_ability')->default(0);    // C
            $table->integer('verbal_ability')->default(0);       // D
            $table->integer('score')->default(0);                // total A+B+C+D+E

            // grade + kategori (berdasarkan usia)
            $table->string('grade')->nullable();
            $table->string('category')->nullable();

            // waktu ujian (jika dibutuhkan)
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();

            $table->boolean('is_finish')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spm_results');
    }
};
