@extends('layouts.template')

@section('title', 'Daftar Customer Status Hold')

@section('content')
<div class="card card-outline card-danger">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar Customer Status <span class="text-danger">Hold</span></h3>
         <a href="javascript:void(0)" onclick="modalAction('{{ route('broadcast.hold') }}')" class="btn btn-sm btn-primary">
            🚀 Broadcast Hold
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table-hold">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>ID Interaksi</th>
                    <th>Kode Customer</th>
                    <th>Nama Customer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal --}}
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    $(document).ready(function () {
        $('#table-hold').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('dashboard.hold') }}", // route untuk data hold
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'interaksi_id', name: 'interaksi_id' },
                { data: 'customer_kode', name: 'customer_kode' },
                { data: 'customer_nama', name: 'customer_nama' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ]
        });
    });
</script>
@endpush
