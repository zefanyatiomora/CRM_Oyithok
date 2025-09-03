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
        // Buat tabel surveys
        Schema::create('surveys', function (Blueprint $table) {
            $table->id('survey_id');
            $table->unsignedBigInteger('interaksi_id');
            $table->string('alamat_survey');
            $table->dateTime('jadwal_survey');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('interaksi_id')->references('interaksi_id')->on('interaksi')->onDelete('cascade');
        });

        // Hapus kolom jadwal_survey dari tabel interaksis
        Schema::table('interaksi', function (Blueprint $table) {
            if (Schema::hasColumn('interaksi', 'jadwal_survey')) {
                $table->dropColumn('jadwal_survey');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tambah kolom jadwal_survey kembali ke interaksis
        Schema::table('interaksis', function (Blueprint $table) {
            $table->dateTime('jadwal_survey')->nullable();
        });

        // Drop tabel surveys
        Schema::dropIfExists('surveys');
    }
};
