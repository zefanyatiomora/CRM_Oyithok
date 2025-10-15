<div class="modal-header bg-wallpaper-gradient text-white">
    <h5 class="modal-title">Tambah Survey</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" class="text-white">&times;</span>
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
            <input type="text" class="form-control" id="jadwal_survey" name="jadwal_survey"
                placeholder="dd-mm-yyyy, hh:mm WIB"
                value="{{ old('jadwal_survey', \Carbon\Carbon::now()->format('d-m-Y, H:i')) . ' WIB' }}" required>
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
    $("#form-create-survey").submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append("interaksi_id", $("#interaksi_id").val());

        // Ambil dan ubah format tanggal (d-m-Y, H:i WIB -> Y-m-d H:i:s)
        let tglInput = $("#jadwal_survey").val().trim();

        if (tglInput) {
            // Pisahkan tanggal dan waktu menggunakan koma
            let [tgl, waktu] = tglInput.split(',');
            if (tgl && waktu) {
                tgl = tgl.trim(); // contoh: "15-10-2025"
                waktu = waktu.replace('WIB', '').trim(); // contoh: "14:30"

                // Pecah tanggal
                let [d, m, y] = tgl.split('-');

                // Format ke MySQL datetime (Y-m-d H:i:s)
                let iso = `${y}-${m}-${d} ${waktu}:00`;

                formData.set("jadwal_survey", iso);
            }
        }

        $.ajax({
            url: "{{ route('survey.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message || 'Survey berhasil disimpan');
                    $("#crudModal").modal('hide');
                    $("#survey-tabel-body").html(res.html);
                    $("a[title='Tambah Survey']").hide();
                } else {
                    Swal.fire("Gagal", res.message || "Terjadi kesalahan saat menyimpan data.",
                        "error");
                }
            },
            error: function(xhr) {
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
