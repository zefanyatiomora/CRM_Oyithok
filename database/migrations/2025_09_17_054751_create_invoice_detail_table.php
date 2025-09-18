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
        Schema::create('invoice_detail', function (Blueprint $table) {
            $table->bigIncrements('detail_id');
            $table->unsignedBigInteger('invoice_id');

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
            $table->foreign('invoice_id')
                  ->references('invoice_id')
                  ->on('invoices')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_detail');
    }
};
