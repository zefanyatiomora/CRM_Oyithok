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
        'awal_id',
        'realtime_id',
        'rincian_id',
        'media',
        'tanggal_chat',
        'pending_until',
        'pending_reason',
        'pending_status',
        'status',
        'tahapan',
        'original_step',
        'skipsteps',
    ];
    protected $casts = [
        'skipsteps' => 'array', //  otomatis cast ke array saat ambil/simpan
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
    public function realtime()
    {
        return $this->hasMany(InteraksiRealtime::class, 'interaksi_id', 'interaksi_id');
    }
    public function interaksi_awal()
    {
        return $this->hasMany(InteraksiAwalModel::class, 'interaksi_id', 'interaksi_id');
    }
    public function rincian()
    {
        return $this->hasMany(RincianModel::class, 'interaksi_id', 'interaksi_id');
    }
}
