<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukModel extends Model
{
    use HasFactory;
    protected $table = 'produks'; // nama tabel di database
    protected $primaryKey = 'produk_id'; // primary key-nya

    protected $fillable = [
        'produk_nama',
        'produk_kode',
        'produk_kategori',
    ];

    public function interaksi()
    {
        return $this->hasMany(InteraksiModel::class, 'produk_id', 'produk_id');
    }

    public function ulasan()
    {
        return $this->hasMany(UlasanModel::class, 'produk_id', 'produk_id');
    }
}
