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
        Schema::table('interaksi', function (Blueprint $table) {
            $table->string('alamat')->nullable()->after('tahapan'); // alamat survey/pasang
            $table->dateTime('waktu_survey')->nullable()->after('alamat'); // tanggal + jam survey
            $table->dateTime('waktu_pasang')->nullable()->after('waktu_survey'); // tanggal + jam pasang
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'waktu_survey', 'waktu_pasang']);
        });
    }
};
