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
        'invoice_id',
        'nomor_invoice',
        'customer_invoice',
        'pesanan_masuk',
        'batas_pelunasan',
        'potongan_harga',
        'cashback',
        'total_akhir',
        'dp',
        'tanggal_dp',
        'tanggal_pelunasan',
        'sisa_pelunasan',
        'catatan',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetailModel::class, 'invoice_id', 'invoice_id');
    }

    public function keterangans(): HasMany
    {
        return $this->hasMany(InvoiceKeteranganModel::class, 'invoice_id', 'invoice_id');
    }
     public function customer()
    {
        return $this->belongsTo(CustomersModel::class, 'customer_invoice', 'customer_id');
    }
}
