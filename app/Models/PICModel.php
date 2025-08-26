<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PICModel extends Model
{
    use HasFactory;

    protected $table = 'pic';
    protected $primaryKey = 'pic_id';
    public $timestamps = true;

    protected $fillable = [
        'pic_nama',
    ];

    /**
     * Relasi: 1 PIC bisa punya banyak interaksi realtime
     */
    public function interaksiRealtimes()
    {
        return $this->hasMany(InteraksiRealtime::class, 'pic_id', 'pic_id');
    }
}
