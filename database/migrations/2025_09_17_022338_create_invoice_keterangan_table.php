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
        Schema::create('invoice_keterangan', function (Blueprint $table) {
            $table->bigIncrements('keterangan_id'); // primary key
            $table->unsignedBigInteger('invoice_id');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('invoice_id')
                  ->references('invoice_id')
                  ->on('invoices')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_keterangan');
    }
};
