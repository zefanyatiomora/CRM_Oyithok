<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceKeteranganModel extends Model
{
    use HasFactory;

    protected $table = 'invoice_keterangan';
    protected $primaryKey = 'keterangan_id';

    protected $fillable = [
        'keterangan_id',
        'invoice_id',
        'keterangan',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceModel::class, 'invoice_id', 'invoice_id');
    }

}