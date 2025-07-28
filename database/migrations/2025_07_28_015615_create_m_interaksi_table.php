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
        Schema::create('m_interaksi', function (Blueprint $table) {
            $table->id('interaksi_id');

            // Foreign Key ke tabel customer
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');

            // Foreign Key ke tabel produk
            $table->unsignedBigInteger('produk_id');
            $table->foreign('produk_id')->references('produk_id')->on('produks')->onDelete('cascade');

            // Tambahan kolom lain
            $table->string('produk_kode')->nullable(); // redundansi (opsional)
            $table->date('tanggal_chat');
            $table->text('identifikasi_kebutuhan')->nullable();
            $table->string('media', 100)->nullable(); // e.g. Whatsapp, Instagram, dll

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_interaksi');
    }
};
