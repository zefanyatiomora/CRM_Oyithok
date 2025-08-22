<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class KategoriModel extends Model
{
    use HasFactory;

    protected $table = 'kategoris';
    protected $primaryKey = 'kategori_id';

    protected $fillable = [
        'kategori_kode',  // Tambahkan kategori_kode di sini
        'kategori_nama',
    ];
    public function produks(): HasMany
    {
        return $this->hasMany(ProdukModel::class, 'kategori_id', 'kategori_id');
    }
    public function interaksi_awal()
{
    return $this->hasMany(InteraksiAwalModel::class, 'kategori_id', 'kategori_id');
}
}
