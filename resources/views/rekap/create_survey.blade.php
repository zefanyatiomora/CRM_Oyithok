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
    if (res.status === 'success') {
        toastr.success(res.message || 'Survey berhasil disimpan');

        // Tutup modal
        $("#crudModal").modal('hide');

        // Update tabel survey
        $("#survey-tabel-body").html(res.html);

        // Sembunyikan tombol tambah kalau survey sudah ada
        $("a[title='Tambah Survey']").hide();
    } else {
        Swal.fire("Gagal", res.message || "Terjadi kesalahan saat menyimpan data.", "error");
    }
},
        error: function (xhr) {
            let msg = "Terjadi kesalahan server.";
            try {
                let json = JSON.parse(xhr.responseText);
                if (json.message) msg = json.message;
            } catch (e) {}
            Swal.fire("Gagal", msg, "error");
            console.error("Server Error:", xhr.responseText);
        }
    });
});
</script>
