<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InteraksiDetailModel extends Model
{
    use HasFactory;

    protected $table = 'interaksi_detail';
    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'interaksi_id',
        'produk_id',
        'produk_nama',
        'tahapan',
        'pic',
        'status',
    ];

    /**
     * Relasi ke Interaksi
     */
    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id', 'interaksi_id');
    }

    /**
     * Relasi ke Produk
     */
    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id', 'produk_id');
    }

    /**
     * Boot method untuk mapping otomatis PIC & Status
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Mapping PIC berdasarkan tahapan
            if ($model->tahapan === 'identifikasi') {
                $model->pic = 'CS';
            } else {
                $model->pic = 'Konsultan';
            }

            // Kalau status belum diisi, set default sesuai tahapan
            if (empty($model->status)) {
                switch ($model->tahapan) {
                    case 'identifikasi':
                        $model->status = 'Ask';
                        break;
                    case 'rincian':
                    case 'survey':
                    case 'pasang':
                    case 'order':
                        $model->status = 'Follow Up';
                        break;
                    case 'done':
                        $model->status = 'Closing ALL';
                        break;
                }
            }
        });
    }
}
