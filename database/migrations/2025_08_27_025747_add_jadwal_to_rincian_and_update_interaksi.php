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
        // Tambah kolom ke tabel rincian
        Schema::table('rincian', function (Blueprint $table) {
            $table->dateTime('jadwal_pasang_kirim')->nullable()->after('deskripsi');
        });

        // Ubah tabel interaksi
        Schema::table('interaksi', function (Blueprint $table) {
            // Drop foreign key constraints dulu
            $table->dropForeign('interaksi_awal_fk');
            $table->dropForeign('interaksi_realtime_fk');
            $table->dropForeign('interaksi_rincian_fk');

            // Hapus kolom
            $table->dropColumn([
                'pending_until',
                'pending_reason',
                'pending_status',
                'awal_id',
                'rincian_id',
                'realtime_id'
            ]);
            $table->string('alamat')->nullable()->after('skipsteps');
            // $table->dateTime('jadwal_survey')->nullable()->after('alamat');
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        // Revert perubahan di tabel rincian
        Schema::table('rincian', function (Blueprint $table) {
            $table->dropColumn(['jadwal_pasang_kirim']);
        });

        // Revert perubahan di tabel interaksi
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dateTime('pending_until')->nullable();
            $table->string('pending_reason')->nullable();
            $table->boolean('pending_status')->default(false);

            // Tambah kembali kolom
            $table->unsignedBigInteger('awal_id')->nullable();
            $table->unsignedBigInteger('realtime_id')->nullable();
            $table->unsignedBigInteger('rincian_id')->nullable();

            // Tambah lagi foreign key
            $table->foreign('awal_id', 'interaksi_awal_fk')->references('awal_id')->on('interaksi_awal')->cascadeOnDelete();
            $table->foreign('realtime_id', 'interaksi_realtime_fk')->references('realtime_id')->on('interaksi_realtime')->cascadeOnDelete();
            $table->foreign('rincian_id', 'interaksi_rincian_fk')->references('rincian_id')->on('rincian')->cascadeOnDelete();

            $table->dropColumn('alamat');
            // $table->dropColumn('jadwal_survey');
        });
    }
};
