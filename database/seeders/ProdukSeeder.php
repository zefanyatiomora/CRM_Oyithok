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

        DB::table('produks')->insert([
            [
                'produk_kode'     => 'W',
                'produk_nama'     => 'Wallpaper Vinyl',
                'produk_kategori' => 'Wall',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'produk_kode'     => 'W',
                'produk_nama'     => 'Woodpanel',
                'produk_kategori' => 'Wall',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'produk_kode'     => 'W',
                'produk_nama'     => 'Wallmoulding',
                'produk_kategori' => 'Wall',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'produk_kode'     => 'SK',
                'produk_nama'     => 'Stiker Kaca One Way',
                'produk_kategori' => 'Stiker Kaca',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'produk_kode'     => 'SK',
                'produk_nama'     => 'Stiker Kaca Sandblast',
                'produk_kategori' => 'Stiker Kaca',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ]);
    }
}
