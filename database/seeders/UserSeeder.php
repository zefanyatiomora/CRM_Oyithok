<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 2,
                'level_id' => 2,
                'username' => 'anisa',
                'nama' => 'Anisa Rahman',
                'alamat' => 'Jl.Raya Bululawang',
                'nohp' => '085859133815',
                'password' => Hash::make('123456'),
            ],
            [
                'user_id' => 1,
                'level_id' => 1,
                'username' => 'oktrin',
                'nama' => 'Oktrin Rustika',
                'alamat' => 'Villa Bukit Tidar',
                'nohp' => '0812345677',
                'password' => Hash::make('123456'),
            ],
        ];
        DB::table('m_user')->insert($data);
    }
}
