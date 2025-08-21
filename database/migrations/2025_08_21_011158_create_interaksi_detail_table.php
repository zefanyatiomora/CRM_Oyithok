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
        Schema::create('interaksi_detail', function (Blueprint $table) {
            $table->id('detail_id');
            $table->unsignedBigInteger('interaksi_id');
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->string('produk_nama', 150)->nullable();

            // Tahapan sesuai select (wajib diisi)
            $table->enum('tahapan', [
                'identifikasi',
                'rincian',
                'survey',
                'pasang',
                'order',
                'done'
            ]);

            // PIC wajib diisi
            $table->string('pic', 100);

            // Status sesuai pilihan (wajib diisi)
            $table->enum('status', [
                'Ask',
                'Follow Up',
                'Closing Survey',
                'Closing Pasang',
                'Closing Product',
                'Closing ALL'
            ]);

            $table->timestamps();

            // Relasi ke tabel interaksi
            $table->foreign('interaksi_id')
                ->references('interaksi_id')
                ->on('interaksi')
                ->onDelete('cascade'); 

            // Relasi ke tabel produk
            $table->foreign('produk_id')
                ->references('produk_id')
                ->on('produks') // pastikan nama tabel sama dengan yang ada di database
                ->onDelete('set null'); // kalau produk dihapus, biar produk_id jadi null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interaksi_detail');
    }
};
