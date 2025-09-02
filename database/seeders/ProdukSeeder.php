<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produks')->insert([
            // kategori_id 1 (dari 7)
            ['kategori_id' => 1, 'produk_nama' => 'Seri C', 'satuan' => 'roll'],
            ['kategori_id' => 1, 'produk_nama' => 'Seri B', 'satuan' => 'roll'],
            ['kategori_id' => 1, 'produk_nama' => 'Seri AS', 'satuan' => 'roll'],
            ['kategori_id' => 1, 'produk_nama' => 'Seri S', 'satuan' => 'roll'],
            ['kategori_id' => 1, 'produk_nama' => 'Seri Ch', 'satuan' => 'roll'],

            // kategori_id 2 (dari 8)
            ['kategori_id' => 2, 'produk_nama' => 'vinyl tekstur', 'satuan' => 'm2'],
            ['kategori_id' => 2, 'produk_nama' => 'vinyl halus', 'satuan' => 'm2'],

            // kategori_id 3 (dari 9)
            ['kategori_id' => 3, 'produk_nama' => 'wallpaper stiker', 'satuan' => 'roll'],

            // kategori_id 4 (dari 10)
            ['kategori_id' => 4, 'produk_nama' => 'wallmoulding', 'satuan' => 'm'],
            ['kategori_id' => 4, 'produk_nama' => 'wallpanel', 'satuan' => 'pcs'],
            ['kategori_id' => 4, 'produk_nama' => 'kaca bevel', 'satuan' => 'm'],

            // kategori_id 5 (dari 11)
            ['kategori_id' => 5, 'produk_nama' => 'pvc 2mm', 'satuan' => 'dus'],
            ['kategori_id' => 5, 'produk_nama' => 'pvc 3mm', 'satuan' => 'dus'],
            ['kategori_id' => 5, 'produk_nama' => 'spc klik', 'satuan' => 'm2'],

            // kategori_id 6 (dari 12)
            ['kategori_id' => 6, 'produk_nama' => '2,5 cm', 'satuan' => 'm2'],
            ['kategori_id' => 6, 'produk_nama' => '3 cm', 'satuan' => 'm2'],

            // kategori_id 7 (dari 13)
            ['kategori_id' => 7, 'produk_nama' => 'sandblast', 'satuan' => 'm2'],
            ['kategori_id' => 7, 'produk_nama' => 'one way mirror 80%', 'satuan' => 'm2'],
            ['kategori_id' => 7, 'produk_nama' => 'one way mirror 90%', 'satuan' => 'm2'],
            ['kategori_id' => 7, 'produk_nama' => 'one way vision', 'satuan' => 'm2'],
            ['kategori_id' => 7, 'produk_nama' => 'sandblast custom', 'satuan' => 'm2'],

            // kategori_id 8 (dari 14)
            ['kategori_id' => 8, 'produk_nama' => 'backdrop', 'satuan' => 'unit'],
            ['kategori_id' => 8, 'produk_nama' => 'kitchenset', 'satuan' => 'unit'],
            ['kategori_id' => 8, 'produk_nama' => 'plafon', 'satuan' => 'unit'],
            ['kategori_id' => 8, 'produk_nama' => 'partisi', 'satuan' => 'unit'],

            // kategori_id 9 (dari 15)
            ['kategori_id' => 9, 'produk_nama' => 'Jasa pasang wallpaper vinyl roll', 'satuan' => 'roll'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa pasang wallpaper custom', 'satuan' => 'm2'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa pasang wallpaper stiker', 'satuan' => 'roll'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa pasang aksesoris dinding', 'satuan' => '-'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa pasang lantai vinyl', 'satuan' => 'm2'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa pasang rumput sintetis', 'satuan' => 'm2'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa pasang stiker kaca', 'satuan' => 'm2'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa bongkar wallpaper vinyl roll', 'satuan' => 'roll'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa bongkar wallpaper custom', 'satuan' => 'm2'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa bongkar wallpaper stiker', 'satuan' => 'roll'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa bongkar aksesoris dinding', 'satuan' => '-'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa bongkar lantai vinyl', 'satuan' => 'm2'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa bongkar rumput sintetis', 'satuan' => 'm2'],
            ['kategori_id' => 9, 'produk_nama' => 'Jasa bongkar stiker kaca', 'satuan' => 'm2'],
        ]);
    }
}
