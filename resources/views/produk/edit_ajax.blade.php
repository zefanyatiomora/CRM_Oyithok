<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-wallpaper-gradient text-white">
            <h5 class="modal-title">Edit Produk</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="text-white">&times;</span>
            </button>
        </div>
        <form id="formEditProduk">
            <div class="modal-body">
                <div class="form-group">
                    <label for="produk_nama">Nama Produk</label>
                    <input type="text" name="produk_nama" class="form-control" value="{{ $produk->produk_nama }}" required>
                </div>
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select name="kategori_id" class="form-control" required>
                        @foreach($kategori as $k)
                            <option value="{{ $k->kategori_id }}" @if($produk->kategori_id == $k->kategori_id) selected @endif>{{ $k->kategori_nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <input type="text" name="satuan" class="form-control" value="{{ $produk->satuan }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
$('#formEditProduk').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: "{{ url('/produk/'.$produk->produk_id.'/update_ajax') }}",
        type: 'POST',
        data: formData,
        success: function(res) {
            if(res.status) {
                $('#myModal').modal('hide');
                dataProduk.ajax.reload(null, false);

                // Popup sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                // Popup gagal
                let errors = '';
                $.each(res.msgField, function(key, value) {
                    errors += value + '\n';
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errors
                });
            }
        },
        error: function(err) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan sistem'
            });
        }
    });
});
</script>
