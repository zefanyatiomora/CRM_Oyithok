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
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id('ulasan_id');

            // Foreign key ke tabel interaksi
            $table->unsignedBigInteger('interaksi_id');
            $table->foreign('interaksi_id')->references('interaksi_id')->on('interaksi')->onDelete('cascade');

            // Foreign key ke tabel customer
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');

            // Foreign key ke tabel produk
            $table->unsignedBigInteger('produk_id');
            $table->foreign('produk_id')->references('produk_id')->on('produks')->onDelete('cascade');

            $table->string('ulasan', 500); // bisa disesuaikan panjangnya
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};
