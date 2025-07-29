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
        'admin_id',       // jika ada
        'tanggal',
        'tahapan',        // misalnya: 'CS', 'Konsultan'
        'catatan',        // jika ada catatan interaksi
    ];

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

    public function ulasan()
    {
        return $this->hasOne(UlasanModel::class, 'interaksi_id', 'interaksi_id');
    }

    // Relasi ke Followup (jika interaksi bisa memiliki banyak follow-up)
    public function followups()
    {
        return $this->hasMany(FollowupModel::class, 'interaksi_id', 'interaksi_id');
    }
}
