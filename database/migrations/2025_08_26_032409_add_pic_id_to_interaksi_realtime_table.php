<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interaksi_realtime', function (Blueprint $table) {
            $table->unsignedBigInteger('pic_id')->nullable()->after('realtime_id'); 
            $table->foreign('pic_id')->references('pic_id')->on('pic')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('interaksi_realtime', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
            $table->dropColumn('pic_id');
        });
    }
};
