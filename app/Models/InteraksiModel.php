<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class InteraksiModel extends Model
{
    use HasFactory;

    protected $table = 'interaksi'; // nama tabel di database
    protected $primaryKey = 'interaksi_id'; // ganti sesuai nama PK kamu

    protected $fillable = [
        'customer_id',
        'produk_id',
        'produk_kode',
        'produk_nama',
        'tanggal_chat',
        'identifikasi_kebutuhan',
        'media',
        'item_type',
        'follow_up',
        'tahapan',
        'pic',
        'alamat',
        'waktu_survey',
        'waktu_pasang',
        'pending_until',
        'pending_reason',
        'pending_status',
    ];

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(CustomersModel::class, 'customer_id', 'customer_id');
    }

    // Relasi ke Produk
    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id', 'produk_id', 'produk_nama');
    }

    public function ulasan()
    {
        return $this->hasOne(UlasanModel::class, 'interaksi_id', 'interaksi_id');
    }
}
