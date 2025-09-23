<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('interaksi_id')->nullable()->after('invoice_id');
            $table->index('interaksi_id');
            $table->foreign('interaksi_id')
                  ->references('interaksi_id')
                  ->on('interaksi')
                  ->onDelete('cascade'); // pilih set null atau cascade sesuai kebijakan
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['interaksi_id']);
            $table->dropIndex(['interaksi_id']);
            $table->dropColumn('interaksi_id');
    });
}
};
