<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukMotifModel extends Model
{
    use HasFactory;

    protected $table = 'produk_motif';
    protected $primaryKey = 'motif_id';
    protected $fillable = [
        'produk_id',
        'motif_kode',
        'motif_nama',
        'motif_deskripsi'
    ];

    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id', 'produk_id');
    }
}
