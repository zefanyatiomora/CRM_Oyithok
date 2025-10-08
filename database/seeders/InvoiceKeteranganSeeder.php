<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceKeteranganSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('invoice_keterangan')->updateOrInsert(
            ['keterangan_id' => 1],
            [
                'keterangan' => "DP yang sudah masuk tidak dapat direfund dengan alasan apapun.\n" .
                    "Barang tidak dapat diretur atau dikembalikan.\n" .
                    "Jika terjadi batal/tukar/retur, maka pembayaran hangus secara profesional.\n" .
                    "Barang wajib dipasang maksimal 1 bulan setelah tanggal pembelian (untuk wallpaper).\n" .
                    "Harga di atas tanpa PPN.\n" .
                    "Jika pemesanan produk dengan jasa pemasangan wajib melakukan pembayaran DP 60% di awal, pelunasan dilakukan setelah pemasangan selesai.\n" .
                    "Pengiriman tanpa jasa pasang wajib melakukan pembayaran DP 70% di awal, pelunasan dilakukan setelah barang siap dan sebelum barang dikirim.\n" .
                    "Jika pemesanan jasa pasang wajib melakukan pembayaran DP 60%-70% (*syarat dan ketentuan berlaku) di awal, pelunasan dilakukan setelah pemasangan selesai.\n" .
                    "Peraturan ini berlaku setelah nota dibuat dan telah dijelaskan oleh Admin.",

                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
