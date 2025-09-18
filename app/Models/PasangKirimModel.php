<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasangKirimModel extends Model
{
    use HasFactory;

    protected $table = 'pasang_kirim';
    protected $primaryKey = 'pasangkirim_id';

    protected $fillable = [
        'interaksi_id',
        'produk_id',
        'kuantitas',
        'deskripsi',
        'jadwal_pasang_kirim',
        'alamat',
        'status',
    ];

    /**
     * Relasi ke Produk
     */
    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id', 'produk_id');
    }

    /**
     * Relasi ke Interaksi
     */
    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id', 'interaksi_id');
    }
    public function invoiceDetail()
    {
        return $this->hasOne(InvoiceDetailModel::class, 'pasangkirim_id', 'pasangkirim_id');
    }
}
