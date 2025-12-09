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
        Schema::create('papikostick_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 9 aspek
            $table->integer('result_orientation')->default(0);
            $table->integer('flexibility')->default(0);
            $table->integer('systematic_work')->default(0);
            $table->integer('achievement_motivation')->default(0);
            $table->integer('cooperation')->default(0);
            $table->integer('interpersonal_skills')->default(0);
            $table->integer('emotional_stability')->default(0);
            $table->integer('self_development')->default(0);
            $table->integer('managing_change')->default(0);

            // G C N
            $table->integer('g_c_n_score')->default(0);
            $table->string('g_c_n_conclusion')->nullable();

            // A W S K
            $table->integer('a_w_s_k_score')->default(0);
            $table->string('a_w_s_k_conclusion')->nullable();

            // Z C
            $table->integer('z_c_score')->default(0);
            $table->string('z_c_conclusion')->nullable();

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
        Schema::dropIfExists('papikostick_results');
    }
};
