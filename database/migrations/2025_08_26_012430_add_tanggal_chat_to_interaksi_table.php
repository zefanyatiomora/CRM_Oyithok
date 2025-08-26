<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->date('tanggal_chat')->nullable()->after('media'); // sesuaikan posisi
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropColumn('tanggal_chat');
        });
    }
};
