<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceKeteranganModel extends Model
{
    use HasFactory;

    protected $table = 'invoice_keterangan';
    protected $primaryKey = 'keterangan_id';

    protected $fillable = [
        'keterangan_id',
        'keterangan',
    ];
}
