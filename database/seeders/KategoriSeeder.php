<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategoris')->insert([
            ['kategori_kode' => 'WR',  'kategori_nama' => 'Wallpaper Vinyl Roll'],
            ['kategori_kode' => 'WC',  'kategori_nama' => 'Wallpaper Custom'],
            ['kategori_kode' => 'WS',  'kategori_nama' => 'Wallpaper Stiker'],
            ['kategori_kode' => 'LV',  'kategori_nama' => 'Lantai Vinyl'],
            ['kategori_kode' => 'SK',  'kategori_nama' => 'Stiker Kaca'],
            ['kategori_kode' => 'RS',  'kategori_nama' => 'Rumput Sintetis'],
            ['kategori_kode' => 'AD',  'kategori_nama' => 'Aksesoris Dinding'],
            ['kategori_kode' => 'IA',  'kategori_nama' => 'Interior Arsitektur'],
            ['kategori_kode' => 'JP',  'kategori_nama' => 'Jasa Pasang'],
        ]);
    }
}
