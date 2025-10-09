@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Card Daftar Customer -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-wallpaper-gradient d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-users mr-2"></i> Daftar Customer
            </h3>
        </div>

        <div class="card-body">
            {{-- Notifikasi --}}
            @if(session('success'))
                <script>toastr.success("{{ session('success') }}")</script>
            @endif
            @if(session('error'))
                <script>toastr.error("{{ session('error') }}")</script>
            @endif

            {{-- Tabel --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm" id="table-customers">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Kode Customer</th>
                            <th>Nama Customer</th>
                            <th style="width: 120px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>
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
    var dataCustomers;
    function modalAction(url = '') {
    $('#myModal').load(url, function () {
        $('#myModal').modal('show');
    });
}
    $(document).ready(function () {
        dataCustomers = $('#table-customers').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ url('customers/list') }}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "customer_kode", orderable: true, searchable: true },
                { data: "customer_nama", orderable: true, searchable: true },
                { data: "aksi", className: "text-center", orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
            },
            responsive: true,
            dom: '<"d-flex justify-content-between mb-2"fB>rt<"d-flex justify-content-between"lip>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Data Customer',
                    filename: 'Data_Customer',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Data Customer',
                    filename: 'Data_Customer',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    customize: function (doc) {
                        // Judul di tengah
                        doc.styles.title = {
                            fontSize: 14,
                            bold: true,
                            alignment: 'center',
                            margin: [0, 0, 0, 15]
                        };

                        // Header tabel
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 11,
                            color: 'white',
                            fillColor: '#8147be',
                            alignment: 'center'
                        };

                        // Isi tabel striping
                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) { return .5; };
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        objLayout['paddingLeft'] = function(i) { return 4; };
                        objLayout['paddingRight'] = function(i) { return 4; };
                        doc.content[1].layout = objLayout;

                        // Margin halaman
                        doc.pageMargins = [40, 60, 40, 40];

                        // Footer nomor halaman
                        doc.footer = function (currentPage, pageCount) {
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
