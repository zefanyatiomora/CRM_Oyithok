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
        Schema::table('invoice_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('pasangkirim_id')->nullable()->after('invoice_id');

            // kalau ada tabel relasi, misalnya tabel `pasangkirim`
            $table->foreign('pasangkirim_id')
                  ->references('pasangkirim_id')
                  ->on('pasang_kirim')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_detail', function (Blueprint $table) {
            $table->dropForeign(['pasangkirim_id']);
            $table->dropColumn('pasangkirim_id');
        });
    }
};
