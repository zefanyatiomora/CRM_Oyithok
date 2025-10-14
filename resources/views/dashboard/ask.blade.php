@extends('layouts.template')

@section('title', 'Daftar Customer Status ASK')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            Daftar Customer Status <span class="text-primary">ASK</span>
        </h3>
        <button class="btn btn-sm btn-success" onclick="modalAction('{{ route('ask.broadcast') }}')">
            <i class="fas fa-paper-plane"></i> 🚀 Broadcast Closing
        </button>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table-interaksi">
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
{{-- kalau butuh style tambahan bisa taruh di sini --}}
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    $(document).ready(function () {
        $('#table-interaksi').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('dashboard.ask') }}", // route untuk data interaksi status ASK
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
