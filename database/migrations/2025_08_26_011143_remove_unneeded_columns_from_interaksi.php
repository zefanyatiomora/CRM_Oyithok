<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            // Drop foreign key dulu
            if (Schema::hasColumn('interaksi', 'produk_id')) {
                $table->dropForeign('m_interaksi_produk_id_foreign');
            }

            // Hapus kolom yang tidak dibutuhkan
            $columnsToDrop = [
                'produk_id',
                'produk_kode',
                'produk_nama',
                'tanggal_chat',
                'identifikasi_kebutuhan',
                'item_type',
                'tahapan',
                'pic',
                'alamat',
                'waktu_survey',
                'waktu_pasang'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('interaksi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('interaksi', function (Blueprint $table) {
            // Tambahkan kembali kolom jika rollback
            $table->bigInteger('produk_id')->unsigned()->nullable();
            $table->string('produk_kode')->nullable();
            $table->string('produk_nama')->nullable();
            $table->date('tanggal_chat');
            $table->text('identifikasi_kebutuhan')->nullable();
            $table->enum('item_type', ['produk', 'jasa', 'produk+jasa'])->nullable();
            $table->string('tahapan')->nullable();
            $table->string('pic')->nullable();
            $table->string('alamat')->nullable();
            $table->dateTime('waktu_survey')->nullable();
            $table->dateTime('waktu_pasang')->nullable();

            // Restore foreign key
            $table->foreign('produk_id', 'm_interaksi_produk_id_foreign')->references('produk_id')->on('produks')->onDelete('cascade');
        });
    }
};
