<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceModel;
use App\Models\InvoiceModel;
use Barryvdh\DomPDF\Facade\Pdf;

class DataInvoiceController extends Controller
{
    public function index()
    {
        $invoices = InvoiceModel::with(['customer', 'pic'])->get();

        return view('datainvoice.index', [
            'activeMenu' => 'datainvoice',
            'invoices'   => $invoices
        ]);
    }

    public function exportPdf($id)
    {
        $invoice = InvoiceModel::with(['customer', 'pic', 'items.produk', 'payments'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('datainvoice.pdf', compact('invoice'));
        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }
     public function show($id)
    {
        $invoice = DataInvoiceModel::with(['customer', 'pic', 'details.produk'])->findOrFail($id);

        // kalau AJAX request, balikan view partial modal
        if (request()->ajax()) {
            return view('invoices.partials.detail', compact('invoice'))->render();
        }

        // fallback kalau bukan AJAX
        return view('invoices.show', compact('invoice'));
    }

}
