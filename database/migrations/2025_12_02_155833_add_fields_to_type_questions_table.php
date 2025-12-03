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
        Schema::table('type_questions', function (Blueprint $table) {
            $table->integer('duration')->default(60)->after('photo'); // durasi ujian (menit)
            $table->text('description')->nullable()->after('duration'); // deskripsi ujian
            $table->enum('status', ['active', 'inactive'])->default('active')->after('description'); // status ujian
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_questions', function (Blueprint $table) {
            $table->dropColumn(['duration', 'description', 'status']);
        });
    }
};
