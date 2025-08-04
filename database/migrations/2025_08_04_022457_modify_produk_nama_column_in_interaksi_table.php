<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->string('produk_nama')->nullable()->after('produk_kode')->change();
        });
    }

    public function down()
    {
        Schema::table('interaksi', function (Blueprint $table) {
            // Tidak perlu ubah urutan balik, cukup abaikan atau tambahkan jika perlu
        });
    }
};
