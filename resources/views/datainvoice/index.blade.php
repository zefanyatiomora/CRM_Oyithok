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
                            <th>Pesanan Masuk</th>
                            <th>No Invoice</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Sisa Pelunasan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $inv)
                            <tr>
                                <td class="text-center">{{ $inv->pesanan_masuk }}</td>
                                <td>{{ $inv->nomor_invoice }}</td>
                                <td>{{ $inv->customer_invoice ?? '-' }}</td>
                                <td>Rp {{ number_format($inv->total_akhir, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($inv->sisa_pelunasan, 0, ',', '.') }}</td>
                                <td>
    @if (!empty($inv->tanggal_pelunasan))
        <span class="badge badge-success">Lunas</span>
    @else
        <span class="badge badge-danger">Belum Lunas</span>
    @endif
</td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info btn-show-invoice"
                                        data-id="{{ $inv->invoice_id }}">
                                        <i class="fas fa-eye fa-sm"></i> Detail
                                    </button>
                                    <a href="{{ route('invoice.export_pdf', $inv->invoice_id) }}"
                                        class="btn btn-sm btn-danger" target="_blank">
                                        <i class="fas fa-file-pdf fa-sm"></i> PDF
                                    </a>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm"
                                        onclick="openModal('{{ url('/invoice/' . $inv->invoice_id . '/edit') }}')"
                                        title="Edit Invoice">
                                        <i class="fas fa-edit fa-sm"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-detail-content">
                    <!-- Konten AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .bg-wallpaper-gradient {
            background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2);
            border-radius: 15px 15px 0 0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            color: #fff;
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).on('click', '.btn-show-invoice', function() {
            let id = $(this).data('id');
            $.get("{{ route('datainvoice.show', ':id') }}".replace(':id', id), function(res) {
                if (res.status === 'success') {
                    $('#modal-detail-content').html(res.html);
                    $('#detailModal').modal('show');
                } else {
                    toastr.error('Gagal load data invoice');
                }
            }).fail(function() {
                toastr.error('Terjadi kesalahan server');
            });
        });

        $(document).ready(function() {
            $('#invoiceTable').DataTable({
                processing: true,
                responsive: true,
                autoWidth: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
                },
                dom: '<"d-flex justify-content-between mb-2"fB>rt<"d-flex justify-content-between"lip>',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Data Invoice',
                        filename: 'Data_Invoice',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Data Invoice',
                        filename: 'Data_Invoice',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        customize: function(doc) {
                            doc.styles.title = {
                                fontSize: 14,
                                bold: true,
                                alignment: 'center',
                                margin: [0, 0, 0, 15]
                            };
                            doc.styles.tableHeader = {
                                bold: true,
                                fontSize: 11,
                                color: 'white',
                                fillColor: '#8147be',
                                alignment: 'center'
                            };
                            var objLayout = {};
                            objLayout['hLineWidth'] = function(i) {
                                return .5;
                            };
                            objLayout['vLineWidth'] = function(i) {
                                return .5;
                            };
                            objLayout['hLineColor'] = function(i) {
                                return '#aaa';
                            };
                            objLayout['vLineColor'] = function(i) {
                                return '#aaa';
                            };
                            objLayout['paddingLeft'] = function(i) {
                                return 4;
                            };
                            objLayout['paddingRight'] = function(i) {
                                return 4;
                            };
                            doc.content[1].layout = objLayout;
                            doc.pageMargins = [40, 60, 40, 40];
                            doc.footer = function(currentPage, pageCount) {
                                return {
                                    text: currentPage.toString() + ' / ' + pageCount,
                                    alignment: 'center',
                                    fontSize: 9,
                                    margin: [0, 10, 0, 0]
                                };
                            };
                        }
                    }
                ]
            });
        });
    </script>
@endpush
