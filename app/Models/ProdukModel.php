<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ProdukModel extends Model
{
    use HasFactory;
    protected $table = 'produks'; // nama tabel di database
    protected $primaryKey = 'produk_id'; // primary key-nya

    protected $fillable = [
        'kategori_id',
        'produk_kode',
        'produk_nama',
    ];

    public function interaksi()
    {
        return $this->hasMany(InteraksiModel::class, 'produk_id', 'produk_id');
    }

    public function ulasan()
    {
        return $this->hasMany(UlasanModel::class, 'produk_id', 'produk_id');
    }
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }
}
