<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom keterangan menjadi text agar bisa menampung lebih banyak kata.
     */
    public function up(): void
    {
        Schema::table('interaksi_realtime', function (Blueprint $table) {
            $table->text('keterangan')->change();
        });
    }

    /**
     * Kembalikan kolom ke tipe string (255 karakter) jika rollback.
     */
    public function down(): void
    {
        Schema::table('interaksi_realtime', function (Blueprint $table) {
            $table->string('keterangan', 255)->change();
        });
    }
};
