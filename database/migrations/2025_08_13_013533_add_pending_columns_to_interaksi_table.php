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
            $table->date('pending_until')->nullable()->after('waktu_pasang');
            $table->string('pending_reason', 100)->nullable()->after('pending_until');
            $table->enum('pending_status', ['Pending', 'Selesai', 'Batal'])->default('Pending')->after('pending_reason');
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropColumn(['pending_until', 'pending_reason', 'pending_status']);
        });
    }
};
