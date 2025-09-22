@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
       <div class="card-header bg-wallpaper-gradient d-flex align-items-center">
    <h3 class="card-title mb-0">
        <i class="fas fa-file-invoice mr-2"></i> Data Invoice
    </h3>
</div>

        <div class="card-body">
            <table id="invoiceTable" class="table table-bordered table-striped">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>No Invoice</th>
                        <th>Customer</th>
                        <th>PIC</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th> <!-- Kolom baru -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $key => $inv)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ $inv->nomor_invoice }}</td>
                        <td>{{ $inv->customer->customer_nama ?? '-' }}</td>
                        <td>{{ $inv->pic->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                        <td>
                            @if($inv->status == 'paid')
                                <span class="badge badge-success">Lunas</span>
                            @elseif($inv->status == 'unpaid')
                                <span class="badge badge-warning">Belum Lunas</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($inv->status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <!-- Tombol Detail (modal AJAX) -->
                            <button onclick="openModal('{{ route('datainvoice.show', $inv->invoice_id) }}')" 
                                    class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Detail
                            </button>

                            <!-- Tombol Export PDF -->
                            <a href="{{ route('datainvoice.exportPdf', $inv->invoice_id) }}" 
                               class="btn btn-sm btn-danger" target="_blank">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('css')
<style>
    .card-header.bg-gradient-primary {
    background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2) !important;
    border-radius: 15px 15px 0 0;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    color: #fff !important;
}
.bg-wallpaper-gradient {
    background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2);
    border-radius: 15px 15px 0 0;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    color: #fff;
}
    #table-customers th {
        vertical-align: middle;
    }
</style>
@endpush

@push('js')
<script>
    $(function () {
        $('#invoiceTable').DataTable({
            responsive: true,
            autoWidth: false,
            lengthChange: true,
            buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#invoiceTable_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush
