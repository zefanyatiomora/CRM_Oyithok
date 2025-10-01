<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus FK dan kolom pic_id dari interaksi_realtime
        Schema::table('interaksi_realtime', function (Blueprint $table) {
            // hapus foreign key kalau ada
            $table->dropForeign(['pic_id']);
            $table->dropColumn('pic_id');

            // 2. Tambahkan kolom user_id
            $table->unsignedBigInteger('user_id')->nullable()->after('realtime_id');

            // tambahkan FK ke m_user(user_id)
            $table->foreign('user_id')
                ->references('user_id')
                ->on('m_user')
                ->onDelete('cascade');
        });

        // 3. Drop tabel pic
        Schema::dropIfExists('pic');
    }

    public function down(): void
    {
        // rollback
        Schema::create('pic', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::table('interaksi_realtime', function (Blueprint $table) {
            // hapus FK user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // tambahkan kembali kolom pic_id
            $table->unsignedBigInteger('pic_id')->nullable();
            $table->foreign('pic_id')
                ->references('pic_id')
                ->on('pic')
                ->onDelete('cascade');
        });
    }
};
