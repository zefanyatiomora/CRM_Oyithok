<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            // Tambah kolom baru
            $table->unsignedBigInteger('awal_id')->nullable()->after('interaksi_id');
            $table->unsignedBigInteger('realtime_id')->nullable()->after('awal_id');
            $table->unsignedBigInteger('rincian_id')->nullable()->after('realtime_id');

            // Tambah foreign key
            $table->foreign('awal_id', 'interaksi_awal_fk')
                ->references('awal_id')->on('interaksi_awal')->onDelete('cascade');

            $table->foreign('realtime_id', 'interaksi_realtime_fk')
                ->references('realtime_id')->on('interaksi_realtime')->onDelete('cascade');

            $table->foreign('rincian_id', 'interaksi_rincian_fk')
                ->references('rincian_id')->on('rincian')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign('interaksi_awal_fk');
            $table->dropForeign('interaksi_realtime_fk');
            $table->dropForeign('interaksi_rincian_fk');

            // Drop kolom
            $table->dropColumn(['awal_id', 'realtime_id', 'rincian_id']);
        });
    }
};
