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
            // tambahkan kolom original_step, bisa null dulu
            $table->unsignedInteger('original_step')->nullable()->after('tahapan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropColumn('original_step');
        });
    }
};
