<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class CustomersModel extends Model
{
    use HasFactory;
    protected $table = 'customers'; // nama tabel di database
    protected $primaryKey = 'customer_id'; // primary key-nya

    protected $fillable = [
        'customer_kode',
        'customer_nama',
        'customer_alamat',
        'customer_nohp',
        'informasi_media',
        'loyalty_point',
        'total_transaction',
        'total_cash_spent'
    ];

    // Relasi: Customer punya banyak interaksi, ulasan, follow-up, dll (jika ada)
    public function interaksi()
    {
        return $this->hasMany(InteraksiModel::class, 'customer_id', 'customer_id');
    }
    // App\Models\CustomersModel.php
    public function refreshLoyalty()
    {
        // eager load relasi
        $this->loadMissing('interaksi.pasang.invoiceDetail.invoice');

        // hitung total transaksi closing
        $totalTransaction = $this->interaksi
            ->where('status', 'closing')
            ->count();

        // hitung total cash spent
        $totalCashSpent = $this->interaksi
            ->where('status', 'closing')
            ->sum(function ($interaksi) {
                if (
                    $interaksi->pasang &&
                    $interaksi->pasang->invoiceDetail &&
                    $interaksi->pasang->invoiceDetail->invoice
                ) {
                    return $interaksi->pasang->invoiceDetail->invoice->total_akhir;
                }
                return 0;
            });

        // update field di tabel
        $this->update([
            'total_transaction' => $totalTransaction,
            'total_cash_spent'  => $totalCashSpent,
        ]);
        Log::info('Hitung loyalty', [
            'customer_id' => $this->customer_id,
            'closing_interaksi' => $this->interaksi->where('status', 'closing')->pluck('interaksi_id'),
            'totalTransaction' => $totalTransaction,
            'totalCashSpent' => $totalCashSpent,
        ]);
    }
}
