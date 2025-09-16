<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePaymentModel extends Model
{
    use HasFactory;

    protected $table = 'invoice_payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount',
        'method',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceModel::class, 'invoice_id', 'invoice_id');
    }
}