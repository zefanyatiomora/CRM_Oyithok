<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('followup', function (Blueprint $table) {
            $table->string('follow_up')->nullable()->after('pic');
            $table->string('close')->nullable()->after('follow_up');
        });
    }

    public function down(): void
    {
        Schema::table('followup', function (Blueprint $table) {
            $table->dropColumn(['follow_up', 'close']);
        });
    }
};