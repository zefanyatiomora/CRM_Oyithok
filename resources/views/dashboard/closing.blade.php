@extends('layouts.template')

@section('title', 'Daftar Customer Status Closing')

@section('content')
<div class="card card-outline card-success">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar Customer Status <span class="text-success">Closing</span></h3>
        <a href="javascript:void(0)" onclick="modalAction('{{ route('broadcast.closing') }}')" class="btn btn-sm btn-primary">
    🚀 Broadcast Closing
</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table-closing">
            <thead class="text-center">
                <tr>
                    <th>No</th>
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
        $('#table-closing').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('dashboard.closing') }}", // route untuk data closing
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'customer_kode', name: 'customer_kode' },
                { data: 'customer_nama', name: 'customer_nama' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ]
        });
    });
</script>
@endpush
