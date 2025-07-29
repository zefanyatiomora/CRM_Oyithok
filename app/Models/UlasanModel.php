<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UlasanModel extends Model
{
    use HasFactory;

    protected $table = 'ulasan'; // nama tabel
    protected $primaryKey = 'ulasan_id'; // sesuaikan jika berbeda

    protected $fillable = [
        'interaksi_id',
        'customer_id',
        'produk_id',
        'kerapian',
        'kecepatan',
        'kualitas_material',
        'profesionalisme',
        'tepat_waktu',
        'kebersihan',
        'kesesuaian_desain',
        'kepuasan_keseluruhan',
        'catatan'
    ];

    // Relasi ke Interaksi
    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id', 'interaksi_id');
    }

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id', 'customer_id');
    }

    // Relasi ke Produk
    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id', 'produk_id');
    }
}
