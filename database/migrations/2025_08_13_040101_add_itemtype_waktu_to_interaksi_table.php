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
            $table->enum('item_type', ['produk', 'jasa', 'produk+jasa'])
                ->nullable()
                ->after('customer_id'); // letak sesuai kebutuhan
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }
};
