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
        Schema::table('rincian', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }

    public function down(): void
    {
        Schema::table('rincian', function (Blueprint $table) {
            $table->string('item_type')->nullable();
        });
    }
};
