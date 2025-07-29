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
        Schema::table('ulasan', function (Blueprint $table) {
            // Hapus kolom ulasan lama
            $table->dropColumn('ulasan');

            // Tambahkan kolom-kolom penilaian
            $table->tinyInteger('kerapian')->nullable();
            $table->tinyInteger('kecepatan')->nullable();
            $table->tinyInteger('kualitas_material')->nullable();
            $table->tinyInteger('profesionalisme')->nullable();
            $table->tinyInteger('tepat_waktu')->nullable();
            $table->tinyInteger('kebersihan')->nullable();
            $table->tinyInteger('kesesuaian_desain')->nullable();
            $table->tinyInteger('kepuasan_keseluruhan')->nullable();

            $table->text('catatan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ulasan', function (Blueprint $table) {
            // Tambahkan kembali kolom ulasan lama
            $table->string('ulasan')->nullable();

            // Hapus kolom penilaian baru
            $table->dropColumn([
                'kerapian',
                'kecepatan',
                'kualitas_material',
                'profesionalisme',
                'tepat_waktu',
                'kebersihan',
                'kesesuaian_desain',
                'kepuasan_keseluruhan',
                'catatan',
            ]);
        });
    }
};
