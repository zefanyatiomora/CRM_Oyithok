@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ $page->title }}</h3>
            <a href="{{ url('/kebutuhan') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-cash-register"></i> Tambah Data Customers
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
                        <th>Tanggal Chat</th>
                        <th>Kode Customer</th>
                        <th>Nama</th>
                        <th>Produk</th>
                        <th>Identifikasi Kebutuhan</th>
                        <th>Tahapan</th>
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
                { data: "tanggal_chat", orderable: true, searchable: true },
                { data: "customer.customer_kode", orderable: true, searchable: true },
                { data: "customer.customer_nama", orderable: true, searchable: true },
                { data: "produk_nama", orderable: false, searchable: false },
                { data: "identifikasi_kebutuhan", orderable: false, searchable: false },
                { data: "tahapan", 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        if (data === 'identifikasi') {
                            return `<span class="badge" style="background-color:#FFD580; color:#000;">${data}</span>`;
                        } else if (data === 'rincian') {
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