<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItemModel extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'invoice_id',
        'produk_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceModel::class, 'invoice_id', 'invoice_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id', 'produk_id');
    }
}