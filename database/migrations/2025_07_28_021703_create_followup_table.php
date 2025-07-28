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
        Schema::create('followup', function (Blueprint $table) {
            $table->id('fol_id');

            // Foreign key ke tabel interaksi
            $table->unsignedBigInteger('interaksi_id');
            $table->foreign('interaksi_id')->references('interaksi_id')->on('interaksi')->onDelete('cascade');

            $table->string('tahapan', 100);     // Tahapan follow-up, misal: "Kontak Awal", "Penawaran", dll
            $table->string('pic', 100);         // Person in Charge (penanggung jawab follow-up)
            $table->date('tanggal');            // Tanggal follow-up dilakukan

            $table->timestamps();               // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followup');
    }
};
