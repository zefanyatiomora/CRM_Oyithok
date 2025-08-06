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

            <table class="table table-bordered table-striped table-hover table-sm" id="table-customers">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Customer</th>
                        <th>Nama Customer</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var dataCustomers;
    $(document).ready(function () {
        dataCustomers = $('#table-customers').DataTable({
            serverSide: true,
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
                {
                    data: "customer_kode",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "customer_nama",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "",
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
