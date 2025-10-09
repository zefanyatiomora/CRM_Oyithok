@extends('layouts.template')

@section('title', 'Daftar Customer Status GHOST')

@section('content')
<div class="card card-outline card-secondary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            Daftar Customer Status <span class="text-secondary">GHOST</span>
        </h3>
        {{-- kalau mau ada tombol broadcast khusus ghost bisa aktifkan di sini --}}
        {{-- 
        <button class="btn btn-sm btn-dark" onclick="modalAction('{{ route('ghost.broadcast') }}')">
            <i class="fas fa-paper-plane"></i> 👻 Broadcast Ghost
        </button> 
        --}}
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table-interaksi-ghost">
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

@push('css')
{{-- style tambahan kalau perlu --}}
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    $(document).ready(function () {
        $('#table-interaksi-ghost').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('dashboard.ghost') }}", // route untuk data interaksi status GHOST
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
