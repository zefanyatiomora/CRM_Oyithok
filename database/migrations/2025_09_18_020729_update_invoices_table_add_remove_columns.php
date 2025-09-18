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
        Schema::table('invoices', function (Blueprint $table) {
            // Tambah kolom baru
            $table->decimal('potongan_harga', 15, 2)->default(0)->after('batas_pelunasan');
            $table->decimal('cashback', 15, 2)->default(0)->after('potongan_harga');
            $table->decimal('total_akhir', 15, 2)->after('cashback');
            $table->decimal('dp', 15, 2)->default(0)->after('total_akhir');
            $table->date('tanggal_dp')->nullable()->after('dp');
            $table->date('tanggal_pelunasan')->nullable()->after('tanggal_dp');
            $table->decimal('sisa_pelunasan', 15, 2)->after('tanggal_pelunasan');
            $table->text('catatan')->nullable()->after('sisa_pelunasan');

            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            $table->dropForeign(['pasangkirim_id']);
            $table->dropColumn('pasangkirim_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('pasangkirim_id')->nullable();

            // Hapus kolom tambahan
            $table->dropColumn([
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
};
