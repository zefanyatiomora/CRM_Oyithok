<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_user', function (Blueprint $table) {
            // Tambah kolom ttd dan image(misalnya string untuk simpan path file)
            $table->string('ttd')->nullable()->after('level_id');
            $table->string('image')->nullable()->after('ttd');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            // Hapus kolom ttd dan image
            $table->dropColumn('ttd');
            $table->dropColumn('image');
        });
    }
};
