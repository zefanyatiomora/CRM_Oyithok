@empty($rincian)
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
                <h5><i class="icon fas fa-ban"></i> Data yang Anda cari tidak ditemukan</h5>
            </div>
        </div>
    </div>
</div>
@else
<form action="{{ route('pasang.update', $rincian->rincian_id) }}" method="POST" id="form-edit-pasang">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Pasang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <input type="hidden" name="interaksi_id" value="{{ $rincian->interaksi->interaksi_id }}">
                <input type="hidden" name="rincian_id" value="{{ $rincian->rincian_id }}">

                <!-- Produk -->
                <div class="form-group">
                    <label>Produk</label>
                    <input type="text" class="form-control" 
                        value="{{ $rincian->produk->produk_nama }} {{ $rincian->kuantitas }} {{ $rincian->satuan }} {{ $rincian->deskripsi }}" 
                        readonly>
                </div>

                <!-- Alamat Pasang -->
                <div class="form-group">
                    <label>Alamat Pasang</label>
                    <input type="text" class="form-control" 
                        value="{{ $rincian->interaksi->alamat }}" 
                        readonly>
                </div>

                <!-- Jadwal Pasang Kirim -->
                <div class="form-group">
                    <label>Jadwal Pasang Kirim</label>
                    <input type="datetime-local" name="jadwal_pasang_kirim" id="jadwal_pasang_kirim" 
                        value="{{ $rincian->jadwal_pasang_kirim ? \Carbon\Carbon::parse($rincian->jadwal_pasang_kirim)->format('Y-m-d\TH:i') : '' }}" 
                        class="form-control" required>
                    <small id="error-jadwal_pasang_kirim" class="text-danger"></small>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function () {
    $("#form-edit-pasang").validate({
        rules: {
            produk: { required: true, maxlength: 500 },
            alamat: { required: true, maxlength: 100 },
            jadwal_pasang_kirim: { required: true, date: true },
        },
        submitHandler: function(form) {
        $.ajax({
            url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                // dataBarang.ajax.reload(); // Make sure this matches your DataTable instance for barang
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty
