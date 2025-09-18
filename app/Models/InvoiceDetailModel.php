<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetailModel extends Model
{
    use HasFactory;

    protected $table = 'invoice_detail';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'detail_id',
        'invoice_id',
        'pasangkirim_id',
        'harga_satuan',
        'total',
        'diskon',
        'grand_total',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceModel::class, 'invoice_id', 'id');
    }

    public function pasangKirim(): BelongsTo
    {
        return $this->belongsTo(PasangKirimModel::class, 'pasangkirim_id', 'id');
    }
}