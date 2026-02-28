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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();

            $table->string('order_id')->unique();

            $table->bigInteger('amount');        // total bayar final
            $table->integer('participants');     // jumlah peserta
            $table->integer('ppn');              // nilai ppn
            $table->integer('unique_code');      // kode unik

            $table->string('snap_token')->nullable();
            $table->string('payment_type')->nullable();

            $table->string('status')->default('pending'); // pending, paid, failed

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
