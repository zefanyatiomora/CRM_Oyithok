<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceDetailModel;
use App\Models\CustomersModel;
use App\Models\InvoiceModel;
use Barryvdh\DomPDF\Facade\Pdf;

class DataInvoiceController extends Controller
{
 public function index()
    {
        $invoices = InvoiceModel::with(['details'])->get();

        $breadcrumb = (object) [
            'title' => 'Data Invoice',
            'list'  => ['Data Invoice']
        ];

        return view('datainvoice.index', [
            'activeMenu' => 'datainvoice',
            'breadcrumb' => $breadcrumb,
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
    $invoice = InvoiceModel::with([
        'details.pasang.produk',
        'details.pasang.interaksi',
        'customer'
    ])->findOrFail($id);

    $html = view('datainvoice.detail', compact('invoice'))->render();

    return response()->json([
        'status' => 'success',
        'html'   => $html
    ]);
}
}