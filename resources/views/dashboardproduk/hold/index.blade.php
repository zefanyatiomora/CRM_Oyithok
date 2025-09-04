@extends('layouts.template')

@section('content')
<div class="card card-outline card-warning">
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

        <table class="table table-bordered table-striped table-hover table-sm" id="table-hold">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Chat</th>
                    <th>Kode Customer</th>
                    <th>Nama Customer</th>
                    <th>Kategori Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div> 
@endsection

@push('js') 
<script> 
    function modalAction(url = ''){ 
        $('#myModal').load(url,function(){ 
            $('#myModal').modal('show'); 
        }); 
    }

    $(document).ready(function() { 
        $('#table-hold').DataTable({
            processing: true,
            serverSide: true,      
            ajax: { 
                url: "{{ route('hold.list') }}", 
                type: "POST", 
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: "{{ $tahun }}",
                    bulan: "{{ $bulan }}"
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
