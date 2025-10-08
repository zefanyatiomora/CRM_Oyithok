<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel & kolom ada
        if (!Schema::hasTable('invoice_keterangan') || !Schema::hasColumn('invoice_keterangan', 'invoice_id')) {
            return;
        }

        // Coba drop foreign key dengan nama konvensional dulu
        try {
            Schema::table('invoice_keterangan', function (Blueprint $table) {
                // dropForeign menerima array ['invoice_id'] atau nama constraint
                $table->dropForeign(['invoice_id']);
            });
        } catch (\Throwable $e) {
            // Jika gagal (nama constraint custom atau tidak ada), cari nama constraint di information_schema
            $dbName = DB::getDatabaseName();

            $row = DB::selectOne(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL
                 LIMIT 1",
                [$dbName, 'invoice_keterangan', 'invoice_id']
            );

            if ($row && isset($row->CONSTRAINT_NAME)) {
                $fkName = $row->CONSTRAINT_NAME;
                // drop by raw SQL
                DB::statement("ALTER TABLE `invoice_keterangan` DROP FOREIGN KEY `{$fkName}`");
            }
        }

        // Setelah constraint di-drop, hapus kolom
        Schema::table('invoice_keterangan', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_keterangan', 'invoice_id')) {
                $table->dropColumn('invoice_id');
            }
        });
    }

    public function down(): void
    {
        // rollback: tambahkan kembali kolom dan foreign key (sesuaikan nama referensi kalau beda)
        Schema::table('invoice_keterangan', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_keterangan', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('id');
                // Ganti 'invoices' dan 'id' jika nama tabel/kolom referensi berbeda
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            }
        });
    }
};
