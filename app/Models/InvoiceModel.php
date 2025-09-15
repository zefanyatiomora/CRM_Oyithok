<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceModel extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'pic_id',
        'invoice_date',
        'total_amount',
        'status',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomersModel::class, 'customer_id', 'customer_id');
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(PICModel::class, 'pic_id', 'pic_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItemModel::class, 'invoice_id', 'invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePaymentModel::class, 'invoice_id', 'invoice_id');
    }
}