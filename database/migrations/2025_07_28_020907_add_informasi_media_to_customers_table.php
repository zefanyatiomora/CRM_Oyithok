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
        Schema::table('customers', function (Blueprint $table) {
              $table->enum('informasi_media', ['google', 'medsos', 'offline'])->after('customer_nohp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('customers', function (Blueprint $table) {
        $table->dropColumn('informasi_media');
    });
    }
};
