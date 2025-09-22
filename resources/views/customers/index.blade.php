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
                {
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                { data: "customer_kode", orderable: true, searchable: true },
                { data: "customer_nama", orderable: true, searchable: true },
                {
                    data: "total_transaction",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "total_cash_spent",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
            },
            responsive: true
        });
    });
</script>
@endpush
