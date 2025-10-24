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
    public function export_pdf()
    {
        $invoices = InvoiceModel::select(
                'pesanan_masuk',
                'nomor_invoice',
                'customer_invoice',
                'total_akhir',
                'sisa_pelunasan',
                'tanggal_pelunasan'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('datainvoice.export_pdf', ['invoices' => $invoices]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data invoice ' . date('Y-m-d H:i:s') . '.pdf');
    }

}