<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interaksi_awal', function (Blueprint $table) {
            $table->bigIncrements('awal_id'); // primary key
            $table->unsignedBigInteger('interaksi_id'); // relasi ke tabel interaksi
            $table->unsignedBigInteger('kategori_id'); // relasi ke tabel kategoris
            $table->string('kategori_nama'); // nama kategori
            $table->timestamps();

            // foreign key
            $table->foreign('interaksi_id')
                  ->references('interaksi_id')
                  ->on('interaksi')
                  ->onDelete('cascade');

            $table->foreign('kategori_id')
                  ->references('kategori_id') // pastikan ini sesuai nama PK di tabel kategoris
                  ->on('kategoris')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('interaksi_awal');
    }
};
