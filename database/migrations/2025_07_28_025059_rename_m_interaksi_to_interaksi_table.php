<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('m_interaksi', 'interaksi');
    }

    public function down(): void
    {
        Schema::rename('interaksi', 'm_interaksi');
    }
};