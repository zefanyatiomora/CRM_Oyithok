@extends('layouts.template')

@section('content')
<div class="card card-outline">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="fas fa-comments mr-2"></i> {{ $page->title }}
        </h3>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm" id="table-ask">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Chat</th>
                        <th>Kode Customer</th>
                        <th>Nama</th>
                        <th>Kategori Produk</th>
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
.card.card-outline .card-header {
    background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2) !important;
    border-radius: 15px 15px 0 0 !important;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15) !important;
    color: #fff !important;
    font-weight: bold;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
}
.card.card-outline .card-header .card-title {
    margin: 0;
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

$(document).ready(function() { 
    $('#table-ask').DataTable({
        processing: true,
        serverSide: true,      
        ajax: { 
            url: "{{ url('ask/list') }}", 
            type: "POST", 
            dataType: "json",
            data: {
                _token: "{{ csrf_token() }}",
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
            { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
            { data: "tanggal_chat", orderable: true, searchable: true },
            { data: "customer.customer_kode", orderable: true, searchable: true },
            { data: "customer.customer_nama", orderable: true, searchable: true },
            { data: "kategori", orderable: true, searchable: true },
            { data: "aksi", orderable: false, searchable: false },
        ] 
    });
}); 
</script>
@endpush
