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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('invoice_id'); // primary key
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('pasangkirim_id');
            $table->unsignedBigInteger('kategori_id');

            $table->string('nomor_invoice');
            $table->string('customer_invoice');
            $table->date('pesanan_masuk')->nullable();
            $table->enum('batas_pelunasan', ['H+1 setelah pasang', 'H-1 sebelum kirim']);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('total_penjualan', 15, 2);
            $table->decimal('potongan_harga', 15, 2)->default(0);
            $table->decimal('cashback', 15, 2)->default(0);
            $table->decimal('total_akhir', 15, 2);
            $table->decimal('dp', 15, 2)->default(0);
            $table->date('tanggal_dp')->nullable();
            $table->date('tanggal_pelunasan')->nullable();
            $table->decimal('sisa_pelunasan', 15, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('customers')
                  ->onDelete('cascade');

            $table->foreign('pasangkirim_id')
                  ->references('pasangkirim_id')
                  ->on('pasang_kirim')
                  ->onDelete('cascade');

            $table->foreign('kategori_id')
                  ->references('kategori_id')
                  ->on('kategoris')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
