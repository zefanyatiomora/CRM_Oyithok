<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InteraksiAwalModel extends Model
{
    use HasFactory;

    protected $table = 'interaksi_awal';
    protected $primaryKey = 'awal_id';
    protected $fillable = [
        'interaksi_id',
        'kategori_id',
        'kategori_nama'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }

    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id', 'interaksi_id');
    }
}
