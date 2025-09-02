<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianModel extends Model
{
    use HasFactory;

    protected $table = 'rincian';
    protected $primaryKey = 'rincian_id';
    protected $fillable = [
        'interaksi_id',
        'produk_id',
        'kuantitas',
        'deskripsi',
    ];

    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id', 'interaksi_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id', 'produk_id');
    }
}
