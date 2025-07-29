<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus dulu kolom yang salah posisinya
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('loyalty_point');
        });

        // Tambah ulang di posisi yang benar
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('loyalty_point')->default(0)->after('informasi_media');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('loyalty_point');
        });

        // Kalau ingin rollback sempurna, bisa tambahkan lagi di posisi sebelumnya
        // $table->integer('loyalty_point')->default(0)->after('customer_nohp');
    }
};