@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
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
            
            <table class="table table-bordered table-striped table-hover table-sm" id="table-rekap">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Chat</th>
                        <th>Kode Customer</th>
                        <th>Nama</th>
                        <th>Produk</th>
                        <th>Identifikasi Kebutuhan</th>
                        <th>Follow Up</th>
                        <th>Close</th>
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
    function modalAction(url = ''){ 
        $('#myModal').load(url,function(){ 
            $('#myModal').modal('show'); 
        }); 
    }

    var dataRekap;

    $(document).ready(function() { 
        dataRekap = $('#table-rekap').DataTable({
            serverSide: true,      
            ajax: { 
                url: "{{ url('rekap/list') }}", 
                type: "POST", 
                dataType: "json",
                data: {
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
                { data: "produk_nama", orderable: false, searchable: false },
                { data: "identifikasi_kebutuhan", orderable: false, searchable: false },
                { data: "follow_up", orderable: false, searchable: false },
                { data: "close", orderable: false, searchable: false },
                { data: "aksi", orderable: false, searchable: false }
            ] 
        });

        // Event listener dropdown follow-up
        $('#table-rekap').on('change', '.follow-up-select', function () {
            let followUpVal = $(this).val();
            let closeVal = '';

            switch (followUpVal) {
                case 'Follow Up 1':
                    closeVal = 'Follow Up 2';
                    break;
                case 'Follow Up 2':
                    closeVal = 'Broadcast';
                    break;
                case 'Closing Survey':
                case 'Closing Pasang':
                case 'Closing Product':
                case 'Closing ALL':
                    closeVal = 'Closing';
                    break;
                default:
                    closeVal = 'Follow Up 1';
                    break;
            }

            let row = $(this).closest('tr');
            row.find('.close-input').val(closeVal);
        });
    }); 
</script> 
@endpush
