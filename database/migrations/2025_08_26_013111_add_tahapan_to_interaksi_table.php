<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->string('tahapan', 50)->nullable()->after('status'); // posisi bisa disesuaikan
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropColumn('tahapan');
        });
    }
};
