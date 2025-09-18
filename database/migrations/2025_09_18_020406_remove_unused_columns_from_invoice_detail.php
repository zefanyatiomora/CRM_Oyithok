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
    Schema::table('invoice_detail', function (Blueprint $table) {
        $table->dropColumn([
            'total_penjualan',
            'potongan_harga',
            'cashback',
            'total_akhir',
            'dp',
            'tanggal_dp',
            'tanggal_pelunasan',
            'sisa_pelunasan',
            'catatan',
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('invoice_detail', function (Blueprint $table) {
        $table->decimal('total_penjualan', 15, 2)->nullable();
        $table->decimal('potongan_harga', 15, 2)->default(0);
        $table->decimal('cashback', 15, 2)->default(0);
        $table->decimal('total_akhir', 15, 2)->nullable();
        $table->decimal('dp', 15, 2)->default(0);
        $table->date('tanggal_dp')->nullable();
        $table->date('tanggal_pelunasan')->nullable();
        $table->decimal('sisa_pelunasan', 15, 2)->nullable();
        $table->text('catatan')->nullable();
    });
}
};
