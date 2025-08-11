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
        Schema::create('rincian', function (Blueprint $table) {
            $table->id('rincian_id');
            $table->unsignedBigInteger('interaksi_id'); // FK ke tabel interaksi
            $table->enum('item_type', ['produk', 'treatment']);
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->unsignedBigInteger('motif_id')->nullable();
            $table->integer('kuantitas')->nullable();
            $table->string('satuan', 50)->nullable(); // roll, m², paket, dll
            $table->text('deskripsi')->nullable(); // lokasi & keterangan tambahan
            $table->timestamps();

            $table->foreign('interaksi_id')
                ->references('interaksi_id')
                ->on('interaksi')
                ->onDelete('cascade');

            $table->foreign('produk_id')
                ->references('produk_id')
                ->on('produks')
                ->onDelete('set null');

            $table->foreign('motif_id')
                ->references('motif_id')
                ->on('produk_motif')
                ->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian');
    }
};
