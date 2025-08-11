<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration: hapus tabel followup.
     */
    public function up(): void
    {
        Schema::dropIfExists('followup');
    }

    /**
     * Rollback migration: kembalikan tabel followup.
     */
    public function down(): void
    {
        Schema::create('followup', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('interaksi_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('follow_up')->nullable();
            $table->string('tahapan')->nullable();
            $table->string('pic')->nullable();
            $table->string('close')->nullable();
            $table->timestamps();
        });
    }
};
