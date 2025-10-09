<div class="modal-header">
    <h5 class="modal-title">Tambah Survey</h5>
    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="form-create-survey" enctype="multipart/form-data">
    @csrf
    <!-- Hidden input untuk ID Interaksi -->
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">

<!-- Jadwal Survey -->
<div class="form-group">
    <label>Jadwal Survey</label>
    <div class="input-group mb-2">
        <input type="datetime-local" class="form-control" id="jadwal_survey" name="jadwal_survey"
            value="{{ old('jadwal_survey', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}" required>
    </div>
    <small id="error-jadwal" class="text-danger"></small>
</div>

    <!-- Alamat Survey -->
    <div class="form-group">
        <label>Alamat Survey</label>
        <textarea name="alamat_survey" id="alamat_survey" class="form-control" rows="3" required></textarea>
        <small id="error-alamat" class="text-danger"></small>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-success">Simpan</button>

</form>


<script>
$(function () {
    // Tombol "Hari Ini" dan "Besok"
    $('#btn-today').click(function() {
        let today = new Date().toISOString().split('T')[0];
        $('#jadwal_survey').val(today);
    });

    $('#btn-tomorrow').click(function() {
        let d = new Date();
        d.setDate(d.getDate() + 1);
        let tomorrow = d.toISOString().split('T')[0];
        $('#jadwal_survey').val(tomorrow);
    });
    // Submit Form dengan AJAX
    $("#form-create-survey").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append("interaksi_id", $("#interaksi_id").val());

        $.ajax({
            url: "{{ route('survey.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success('Survey berhasil disimpan');
                tableRekap.ajax.reload(null, false);

                let interaksiId = $("#interaksi_id").val();
                $("#myModal").load("{{ url('rekap') }}/" + interaksiId + "/show_ajax");

                $("#crudModal").modal('hide');
            },
            error: function (xhr) {
                Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                console.error("Server Error:", xhr.responseText);
            }
        });
    });

});
</script>
