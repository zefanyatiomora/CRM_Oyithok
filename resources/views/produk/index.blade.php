@extends('layouts.template')

@section('content')
  <div class="card card-outline">
        <div class="card-header bg-wallpaper-gradient">
            <h3 class="card-title">
    <i class="fas fa-database mr-2"></i> Data Produk
</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/produk/create_ajax') }}')" class="btn btn-sm btn-success mt-1">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah
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
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div> 
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
        function modalAction(url = ''){ 
            $('#myModal').load(url,function(){ 
                $('#myModal').modal('show'); 
            }); 
        }
        var dataProduk;
        $(document).ready(function() { 
            dataProduk= $('#table-produk').DataTable({
                // serverSide: true, if using server-side processing 
                serverSide: true,      
                ajax: { 
                    "url": "{{ url('produk/list') }}", 
                    "type": "POST", 
                    "dataType": "json"
                }, 
                columns: [ 
                    {
                        // nomor urut from Laravel datatable addIndexColumn() 
                        data: "DT_RowIndex",             
                        className: "text-center", 
                        orderable: false, 
                        searchable: false     
                    },
                    { 
                        data: "kategori.kategori_nama",                
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "produk_nama",                
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "satuan",                
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "aksi",                
                        className: "", 
                        orderable: false,     
                        searchable: false     
                    } 
                ] 
            }); 
        }); 
    </script> 
@endpush
