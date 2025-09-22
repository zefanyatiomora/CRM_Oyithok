@extends('layouts.template')

@section('content')
    <div class="card card-outline">
        <div class="card-header bg-wallpaper-gradient d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-users mr-2"></i> {{ $page->title }}
            </h3>
            <a href="{{ url('/kebutuhan') }}" class="btn btn-light btn-sm shadow-sm">
                <i class="fas fa-cash-register mr-1"></i> Tambah Data Customers
            </a>
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
                        <th>Kode Customer</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
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

@push('js') 
<script> 
    function modalAction(url = ''){ 
        $('#myModal').load(url,function(){ 
            $('#myModal').modal('show'); 
        }); 
    }
    var tableRekap;
    $(document).ready(function() { 
        tableRekap = $('#table-rekap').DataTable({
            processing: true,
            serverSide: true,      
            ajax: { 
                url: "{{ url('rekap/list') }}", 
                type: "POST", 
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: "{{ $tahun }}",
                    bulan: "{{ $bulan }}",
                    interaksi_id: "{{ request('interaksi_id') }}"
                },
                error: function (xhr, error, thrown) {
                    console.error("AJAX error:", error);
                    console.error("Status:", xhr.status);
                    console.error("Response:", xhr.responseText);
                }
            }, 
            columns: [ 
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "customer.customer_kode", orderable: true, searchable: true },
                { data: "customer.customer_nama", orderable: true, searchable: true },
                { data: "status", 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        if (data === 'Ask') {
                            return `<span class="badge" style="background-color:#FFD580; color:#000;">${data}</span>`;
                        } else if (data === 'Follow Up') {
                            return `<span class="badge" style="background-color:#E6CCFF; color:#000;">${data}</span>`;
                        } else {
                            return data ?? '';
                        }
                    }
                },
                { data: "aksi", orderable: false, searchable: false }
            ] 
        });
    }); 
</script> 
@endpush