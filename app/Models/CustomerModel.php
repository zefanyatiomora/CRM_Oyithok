<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    use HasFactory;
    protected $table = 'customers'; // nama tabel di database
    protected $primaryKey = 'customer_id'; // primary key-nya

    protected $fillable = [
        'customer_nama',
        'customer_kode',
        'customer_alamat',
        'customer_nohp',
        'informasi_media',
        'loyalty_point'
        // tambahkan kolom lain sesuai migrasi kamu
    ];

    // Relasi: Customer punya banyak interaksi, ulasan, follow-up, dll (jika ada)
    public function interaksi()
    {
        return $this->hasMany(InteraksiModel::class, 'customer_id', 'customer_id');
    }

    public function ulasan()
    {
        return $this->hasMany(UlasanModel::class, 'customer_id', 'customer_id');
    }
    public function followup()
    {
        return $this->hasMany(FollowupModel::class, 'customer_id', 'customer_id');
    }
}
