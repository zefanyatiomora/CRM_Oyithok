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
            // Tambah kolom skipsteps (JSON) setelah originalstep
            $table->json('skipsteps')->nullable()->after('original_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropColumn('skipsteps');
        });
    }
};
