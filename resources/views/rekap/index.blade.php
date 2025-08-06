@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <table class="table table-bordered table-striped table-hover table-sm" id="table-rekap">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Chat</th>
                        <th>Kode Customer</th>
                        <th>Nama</th>
                        <th>Produk</th>
                        <th>Identifikasi Kebutuhan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div> 
@endsection

@push('css')
@endpush


@push('js') 
    <script> 
        function modalAction(url = ''){ 
            $('#myModal').load(url,function(){ 
                $('#myModal').modal('show'); 
            }); 
        }
        var dataRekap;
        $(document).ready(function() { 
            dataRekap= $('#table-rekap').DataTable({
                // serverSide: true, if using server-side processing 
                serverSide: true,      
                ajax: { 
                    url: "{{ url('rekap/list') }}", 
                    type: "POST", 
                    dataType: "json",
                    data: {
                    tahun: "{{ $tahun }}",
                    bulan: "{{ $bulan }}"
                    },
                    error: function (xhr, error, thrown) {
                    console.error("AJAX error:", error);
                    console.error("Status:", xhr.status);
                    console.error("Response:", xhr.responseText);
                    }
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
                        data: "tanggal_chat",                
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "customer.customer_kode",                
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "customer.customer_nama",                
                        className: "", 
                        orderable: true,     
                        searchable: true     
                    },
                    { 
                        data: "produk_nama",                
                        className: "", 
                        orderable: false,     
                        searchable: false     
                    }, 
                    { 
                        data: "identifikasi_kebutuhan",                
                        className: "", 
                        orderable: false,     
                        searchable: false     
                    } 
                ] 
            }); 
        }); 
    </script> 
@endpush
