@empty($customer)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang Anda cari tidak ditemukan
            </div>
        </div>
    </div>
</div>
@else
<form action="{{ url('/customers/' . $customer->customer_id . '/update') }}" method="POST" id="form-edit-customer">
    @csrf
    @method('PUT')
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal-master">
            <div class="modal-header">
                <h5 class="modal-title">Sunting Data Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form Fields -->
                <div class="form-group">
                    <label>Kode Customer</label>
                    <input type="text" name="customer_kode" class="form-control" value="{{ $customer->customer_kode }}" readonly>
                    <small id="error-customer_kode" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Nama Customer</label>
                    <input type="text" name="customer_nama" class="form-control" value="{{ $customer->customer_nama }}">
                    <small id="error-customer_nama" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="customer_alamat" class="form-control" value="{{ $customer->customer_alamat }}">
                </div>

                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="customer_nohp" class="form-control" value="{{ $customer->customer_nohp }}">
                </div>

                <div class="form-group">
                    <label>Informasi Media</label>
                    <input type="text" name="informasi_media" class="form-control" value="{{ $customer->informasi_media }}" readonly>
                </div>

                <div class="form-group">
                    <label>Loyalty Point</label>
                    <input type="number" name="loyalty_point" class="form-control" value="{{ $customer->loyalty_point }}" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<!-- Script Ajax -->
<script>
$(document).ready(function () {
    $("#form-edit-customer").submit(function (e) {
        e.preventDefault();

        let form = this;

        $.ajax({
            url: form.action,
            method: form.method,
            data: $(form).serialize(),
            success: function (response) {
                $('.error-text').text(''); // bersihkan error lama

                if (response.status) {
                    $('#modal-master').closest('.modal').modal('hide'); // tutup modal
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // reload datatable / halaman
                    });
                } else {
                    // Tampilkan error validasi
                    $.each(response.msgField, function (prefix, val) {
                        $('#error-' + prefix).text(val[0]);
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan server. Silakan coba lagi.'
                });
            }
        });
    });
});
</script>
@endempty
