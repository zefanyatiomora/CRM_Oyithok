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
        'item_type',
        'produk_id',
        'motif_id',
        'kuantitas',
        'satuan',
        'deskripsi'
    ];

    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id', 'interaksi_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id', 'produk_id');
    }

    public function motif()
    {
        return $this->belongsTo(ProdukMotifModel::class, 'motif_id', 'motif_id');
    }
}
