@extends('layouts.template')

@section('content')
<div class="card card-outline shadow-sm border-0">
    <div class="card-header bg-wallpaper-gradient position-relative" style="border-radius: 0.5rem 0.5rem 0 0; padding: 0.75rem 1.25rem;">
        <h3 class="card-title mb-0 fw-bold">
            <i class="fas fa-database me-2"></i> Data Produk
        </h3>

        {{-- Tombol tambah elegan di pojok kanan --}}
<div class="card-tools position-absolute" style="top: 8px; right: 25px;">
    <button onclick="modalAction('{{ url('/produk/create_ajax') }}')" 
            class="btn btn-success btn-sm btn-rounded shadow-sm d-flex align-items-center">
        <i class="fas fa-plus me-1"></i> Tambah
    </button>
</div>
    </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm" id="table-produk">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategori produk</th>
                            <th>Nama produk</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" 
         data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div> 
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
    #table-produk th {
        vertical-align: middle;
    }
</style>
@endpush

<style>
    .btn-rounded {
    border-radius: 50px !important;
    transition: all 0.2s ease-in-out;
}
.btn-rounded:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

@push('js') 
<script> 
    function modalAction(url = ''){ 
        $('#myModal').load(url,function(){ 
            $('#myModal').modal('show'); 
        }); 
    }

    var dataProduk;
    $(document).ready(function() { 
        dataProduk = $('#table-produk').DataTable({
            serverSide: true,      
            ajax: { 
                url: "{{ url('produk/list') }}", 
                type: "POST", 
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }, 
            columns: [ 
                { 
                    data: "DT_RowIndex",             
                    className: "text-center", 
                    orderable: false, 
                    searchable: false     
                },
                { data: "kategori.kategori_nama" },
                { data: "produk_nama" },
                { data: "satuan" },
                { data: "aksi", orderable: false, searchable: false } 
            ],
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
            },
            dom: '<"d-flex justify-content-between mb-2"fB>rt<"d-flex justify-content-between"lip>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Data Produk',
                    filename: 'Data_Produk',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0,1,2,3] // tanpa kolom aksi
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Data Produk',
                    filename: 'Data_Produk',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0,1,2,3] // tanpa kolom aksi
                    },
                    customize: function (doc) {
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
                        objLayout['hLineWidth'] = function(i) { return .5; };
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        objLayout['paddingLeft'] = function(i) { return 4; };
                        objLayout['paddingRight'] = function(i) { return 4; };
                        doc.content[1].layout = objLayout;
                        doc.pageMargins = [40, 60, 40, 40];
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

