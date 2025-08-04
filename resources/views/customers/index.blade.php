@extends('layouts.template')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Daftar Customer</h3>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
            
        <table class="table table-bordered table-striped table-hover table-sm" id="table_customers">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Customer</th>
                    <th>Nama Customer</th>
                    <th>Alamat Customer</th>
                    <th>No HP Customer</th>
                    <th>Media</th>
                    <th>Loyalty Point</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data akan dimuat secara dinamis oleh DataTables -->
            </tbody>
        </table>
    </div>
</div>
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
<style>
    #table_customers th, #table_customers td {
        text-align: center; 
        vertical-align: middle; 
        padding: 10px;
    }

    #table_customers th {
        background-color: #11315F;
        color: white;
        font-weight: bold;
    }

    #table_customers tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Styling untuk modal */
    #myModal .modal-content {
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
    }

    #myModal .modal-header {
        background-color: #11315F;
        color: white;
    }

    #myModal .modal-footer {
        background-color: #f8f9fa;
    }

    /* Animasi shake untuk modal */
    .animate.shake {
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        50% { transform: translateX(5px); }
        75% { transform: translateX(-5px); }
        100% { transform: translateX(0); }
    }
</style>
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    $(document).ready(function() {
        // Cek apakah DataTable sudah diinisialisasi
        if ($.fn.DataTable.isDataTable('#table_customers')) {
            $('#table_customers').DataTable().destroy();
        }

        // Inisialisasi DataTable
        $('#table_customers').DataTable({
            processing: true, // Tampilkan animasi loading
            serverSide: true, // Server-side processing
            ajax: {
                url: "{{ url('customers/list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            columns: [
                { data: "customer_id", name: "customer_id" },
                { data: "customer_kode", name: "customer_kode" },
                { data: "customer_nama", name: "customer_nama" },
                { data: "customer_alamat", name: "customer_alamat" },
                { data: "customer_nohp", name: "customer_nohp" },
                { data: "informasi_media", name: "informasi_media" },
                { data: "loyalty_point", name: "loyalty_point" },
                { data: "aksi", name: "aksi", orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json" // Bahasa Indonesia
            },
            responsive: true // Responsif untuk perangkat kecil
        });
    });
</script>
@endpush
