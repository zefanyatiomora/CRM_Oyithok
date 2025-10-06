<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Hapus constraint unique pada kolom customer_invoice
            $table->dropUnique(['customer_invoice']); // sesuai nama kolom
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Tambahkan kembali constraint unique jika rollback
            $table->unique('customer_invoice');
        });
    }
};
