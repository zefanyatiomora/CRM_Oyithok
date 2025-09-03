<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyModel extends Model
{
    use HasFactory;

    protected $table = 'surveys';
    protected $primaryKey = 'survey_id';

    protected $fillable = [
        'interaksi_id',
        'alamat_survey',
        'jadwal_survey',
        'status',
    ];

    /**
     * Relasi ke tabel interaksi (Many to One).
     */
    public function interaksi()
    {
        return $this->belongsTo(InteraksiModel::class, 'interaksi_id', 'interaksi_id');
    }
}
