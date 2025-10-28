{{-- Jika rincian tidak ditemukan --}}
@empty($rincian)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Data yang Anda cari tidak ditemukan</h5>
            </div>
        </div>
    </div>
</div>
@endempty

{{-- Jika rincian ditemukan, tampilkan konfirmasi hapus --}}
@isset($rincian)
<form action="{{ route('rincian.delete', $rincian->rincian_id) }}" method="POST" id="form-delete-rincian">
    @csrf
    @method('DELETE')
    <div id="modal-master" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Rincian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Hidden input untuk dikirim via AJAX --}}
                <input type="hidden" name="interaksi_id" id="interaksi_id" value="{{ $rincian->interaksi_id }}">

                <p>Apakah Anda yakin ingin menghapus rincian produk ini?</p>
                
                {{-- Menampilkan detail item yang akan dihapus --}}
                <div class="alert alert-light">
                    <strong>Produk:</strong> {{ $rincian->produk->produk_nama ?? 'N/A' }} <br>
                    <strong>Kuantitas:</strong> {{ $rincian->kuantitas }} {{ $rincian->produk->satuan ?? '' }}
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>
</form>
@endisset

<script>
$(document).ready(function () {
    // Menangani submit form konfirmasi hapus
    $("#form-delete-rincian").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let actionUrl = $(this).attr("action");

        $.ajax({
            url: actionUrl,
            type: "POST", // Method tetap POST karena kita menggunakan method spoofing (_method: DELETE)
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.message);

                // Reload datatable di halaman utama
                if (typeof tableRekap !== 'undefined') {
                    tableRekap.ajax.reload(null, false);
                }

                // Ambil interaksi_id untuk me-reload modal utama
                let interaksiId = $("#interaksi_id").val();
                
                // Reload isi modal utama (yang menampilkan daftar rincian)
                $("#myModal").load("{{ url('rekap') }}/" + interaksiId + "/show_ajax");
                
                // Tutup modal konfirmasi ini
                $("#crudModal").modal('hide');
            },
            error: function (xhr) {
                Swal.fire("Gagal", "Terjadi kesalahan saat menghapus data.", "error");
                console.error("Server Error:", xhr.responseText);
            }
        });
    });
});
</script>