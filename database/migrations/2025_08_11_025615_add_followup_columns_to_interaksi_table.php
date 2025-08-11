<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->string('tahapan', 50)->nullable()->after('media');
            $table->string('pic', 100)->nullable()->after('tahapan');
            $table->string('follow_up', 100)->nullable()->after('pic');
            $table->string('close', 100)->nullable()->after('follow_up');
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            $table->dropColumn(['tahapan', 'pic', 'follow_up', 'close']);
        });
    }
};
