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
        Schema::create('produk_motif', function (Blueprint $table) {
            $table->id('motif_id');
            $table->unsignedBigInteger('produk_id');
            $table->string('motif_kode')->nullable(); // contoh: C3988, ST09
            $table->string('motif_nama')->nullable(); // kalau mau kasih nama motif
            $table->text('motif_deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('produk_id')
                ->references('produk_id')
                ->on('produks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_motif');
    }
};
