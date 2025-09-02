<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perubahan schema.
     */
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            // hapus kolom produk_kode
            $table->dropColumn('produk_kode');

            // tambah kolom satuan setelah produk_nama (opsional bisa ganti posisi)
            $table->string('satuan', 50)->after('produk_nama');
        });
    }

    /**
     * Rollback perubahan schema.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            // kembalikan kolom produk_kode
            $table->string('produk_kode', 50)->unique()->after('produk_nama');

            // hapus kolom satuan
            $table->dropColumn('satuan');
        });
    }
};
