@empty($interaksi)
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
<form action="{{ route('survey.update', $interaksi->interaksi_id) }}" method="POST" id="form-edit-survey">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Survey</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">

                <!-- Alamat -->
                <div class="form-group">
                    <label>Alamat Survey</label>
                    <input type="text" name="alamat" id="alamat" 
                           value="{{ $interaksi->alamat }}" 
                           class="form-control" placeholder="Masukkan alamat survey" required>
                    <small id="error-alamat" class="text-danger"></small>
                </div>

                <!-- Jadwal Survey -->
                <div class="form-group">
                    <label>Jadwal Survey</label>
                    <input type="datetime-local" name="jadwal_survey" id="jadwal_survey" 
                           value="{{ $interaksi->jadwal_survey ? \Carbon\Carbon::parse($interaksi->jadwal_survey)->format('Y-m-d\TH:i') : '' }}" 
                           class="form-control" required>
                    <small id="error-jadwal_survey" class="text-danger"></small>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function () {
    $("#form-edit-survey").validate({
        rules: {
            interaksi_id: { required: true, number: true },
            alamat: { required: true, maxlength: 100 },
            jadwal_survey: { required: true, date: true },
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
