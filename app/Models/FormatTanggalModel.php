<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FormatTanggalModel extends Model
{
    // Kalau ada table khusus, tulis disini
    protected $table = 'nama_tabel_kamu';

    // Kalau ada field bertipe date
    protected $dates = ['created_at', 'updated_at', 'tanggal'];

    // Override format tanggal default
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('d-m-Y');
    }
}
