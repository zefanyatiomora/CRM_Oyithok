<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Ambil ID kategori berdasarkan kode
        $kategoriMap = DB::table('kategoris')->pluck('kategori_id', 'kategori_kode');

        DB::table('produks')->insert([
            [
                'produk_kode'     => 'W01',
                'produk_nama'     => 'Wallpaper Vinyl',
                'kategori_id'     => $kategoriMap['W'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'produk_kode'     => 'W02',
                'produk_nama'     => 'Woodpanel',
                'kategori_id'     => $kategoriMap['W'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'produk_kode'     => 'W03',
                'produk_nama'     => 'Wallmoulding',
                'kategori_id'     => $kategoriMap['W'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'produk_kode'     => 'SK01',
                'produk_nama'     => 'Stiker Kaca One Way',
                'kategori_id'     => $kategoriMap['SK'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'produk_kode'     => 'SK02',
                'produk_nama'     => 'Stiker Kaca Sandblast',
                'kategori_id'     => $kategoriMap['SK'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ]);
    }
}
