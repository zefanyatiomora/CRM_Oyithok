@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data PIC</h3>
            <button class="btn btn-success float-right" id="btn-add">+ Tambah PIC</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="pic-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama PIC</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modal-pic" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modal-title">Tambah PIC</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
            <form id="form-pic">
                <input type="hidden" id="pic_id" name="pic_id">
                <div class="form-group">
                    <label for="pic_nama">Nama PIC</label>
                    <input type="text" class="form-control" id="pic_nama" name="pic_nama" required>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="btn-save">Simpan</button>
        </div>
    </div>
  </div>
</div>
@endsection

@push('css')
@endpush

@push('js')
<script>
$(function() {
    // Inisialisasi DataTables
    let table = $('#pic-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('pic.data') }}",
        columns: [
            { data: 'pic_id', name: 'pic_id' },
            { data: 'pic_nama', name: 'pic_nama' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Tombol Tambah
    $('#btn-add').click(function() {
        $('#form-pic')[0].reset();
        $('#pic_id').val('');
        $('#modal-title').text('Tambah PIC');
        $('#modal-pic').modal('show');
    });

    // Simpan Data
    $('#btn-save').click(function() {
        let id = $('#pic_id').val();
        let url = id ? "{{ url('pic/update') }}/" + id : "{{ route('pic.store') }}";
        let method = id ? "POST" : "POST";

        $.ajax({
            url: url,
            type: method,
            data: $('#form-pic').serialize(),
            success: function(res) {
                $('#modal-pic').modal('hide');
                table.ajax.reload(null, false);
                Swal.fire('Sukses', res.message, 'success');
            },
            error: function(err) {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Edit Data
    $(document).on('click', '.editPic', function() {
        let id = $(this).data('id');
        $.get("{{ url('pic/edit') }}/" + id, function(res) {
            $('#pic_id').val(res.pic_id);
            $('#pic_nama').val(res.pic_nama);
            $('#modal-title').text('Edit PIC');
            $('#modal-pic').modal('show');
        });
    });

    // Hapus Data
    $(document).on('click', '.deletePic', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('pic/delete') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        table.ajax.reload(null, false);
                        Swal.fire('Terhapus!', res.message, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
