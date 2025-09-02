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
        /**
         * 1. Create table pasang_kirim
         */
        Schema::create('pasang_kirim', function (Blueprint $table) {
            $table->id('pasangkirim_id');
            $table->unsignedBigInteger('interaksi_id');
            $table->unsignedBigInteger('produk_id');
            $table->integer('kuantitas');
            $table->string('deskripsi', 255)->nullable();
            $table->dateTime('jadwal_pasang_kirim')->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('status', 50)->default('pending');
            $table->timestamps();

            // foreign keys
            $table->foreign('produk_id')
                ->references('produk_id')
                ->on('produks')
                ->onDelete('cascade');

            $table->foreign('interaksi_id')
                ->references('interaksi_id')
                ->on('interaksi')
                ->onDelete('cascade');
        });

        /**
         * 2. Hapus kolom motif_id, satuan, dan jadwal_pasang_kirim dari rincian
         */
        Schema::table('rincian', function (Blueprint $table) {
            if (Schema::hasColumn('rincian', 'motif_id')) {
                $table->dropForeign(['motif_id']);
                $table->dropColumn('motif_id');
            }

            if (Schema::hasColumn('rincian', 'satuan')) {
                $table->dropColumn('satuan');
            }

            if (Schema::hasColumn('rincian', 'jadwal_pasang_kirim')) {
                $table->dropColumn('jadwal_pasang_kirim');
            }
        });

        /**
         * 3. Hapus kolom alamat di interaksi
         */
        Schema::table('interaksi', function (Blueprint $table) {
            if (Schema::hasColumn('interaksi', 'alamat')) {
                $table->dropColumn('alamat');
            }
        });

        /**
         * 4. Drop table produk_motif
         */
        Schema::dropIfExists('produk_motif');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * 1. Drop pasang_kirim
         */
        Schema::dropIfExists('pasang_kirim');

        /**
         * 2. Tambahkan kembali kolom di rincian
         */
        Schema::table('rincian', function (Blueprint $table) {
            $table->unsignedBigInteger('motif_id')->nullable();
            $table->string('satuan', 50)->nullable();
            $table->dateTime('jadwal_pasang_kirim')->nullable();

            $table->foreign('motif_id')
                ->references('id')
                ->on('produk_motif')
                ->onDelete('cascade');
        });

        /**
         * 3. Tambahkan kembali kolom alamat di interaksi
         */
        Schema::table('interaksi', function (Blueprint $table) {
            $table->string('alamat', 255)->nullable();
        });

        /**
         * 4. Buat kembali table produk_motif
         */
        Schema::create('produk_motif', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produk_id');
            $table->string('motif_nama');
            $table->timestamps();

            $table->foreign('produk_id')
                ->references('produk_id')
                ->on('produks')
                ->onDelete('cascade');
        });
    }
};
