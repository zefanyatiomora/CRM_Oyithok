<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InteraksiRealtime extends Model
{
    use HasFactory;

    protected $table = 'interaksi_realtime';
    protected $primaryKey = 'realtime_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'interaksi_id',
        'tanggal',
        'keterangan',
        'pic_id',
    ];

    // Relasi ke model Interaksi
    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id', 'interaksi_id');
    }
       public function pic()
    {
        return $this->belongsTo(PICModel::class, 'pic_id', 'pic_id');
    }
}
