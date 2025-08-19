<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interaksi_realtime', function (Blueprint $table) {
            $table->id('realtime_id');
            $table->unsignedBigInteger('interaksi_id');
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            // FK ke interaksi.interaksi_id
            $table->foreign('interaksi_id')
                  ->references('interaksi_id')
                  ->on('interaksi')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interaksi_realtime');
    }
};
