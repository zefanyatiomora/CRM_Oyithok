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
        'media',
        'tanggal_chat',
        'status',
        'tahapan',
        'original_step',
        'skipsteps',
        'jadwal_survey',
    ];
    protected $casts = [
        'skipsteps' => 'array', //  otomatis cast ke array saat ambil/simpan
    ];

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(CustomersModel::class, 'customer_id', 'customer_id');
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
    public function pasang()
    {
        return $this->hasMany(PasangKirimModel::class, 'interaksi_id', 'interaksi_id');
    }
    public function survey()
    {
        return $this->hasOne(SurveyModel::class, 'interaksi_id', 'interaksi_id');
        // Kalau mau simpan banyak survey per interaksi:
        // return $this->hasMany(Survey::class, 'interaksi_id', 'interaksi_id');
    }
}
