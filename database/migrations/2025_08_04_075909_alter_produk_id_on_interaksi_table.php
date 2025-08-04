<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            // Drop foreign key jika ada
            DB::statement('ALTER TABLE interaksi DROP FOREIGN KEY IF EXISTS interaksi_produk_id_foreign');

            // Ubah kolom
            $table->unsignedBigInteger('produk_id')->nullable()->change();

            // Tambahkan kembali foreign key
            $table->foreign('produk_id')->references('produk_id')->on('produks')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
            $table->bigInteger('produk_id')->nullable()->change();
        });
    }
};
