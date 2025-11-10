<div class="modal-header bg-wallpaper-gradient text-white">
    <h5 class="modal-title">Tambah Survey</h5>
</div>
<div class="modal-body">
<form id="form-create-survey" enctype="multipart/form-data">
    @csrf
    <!-- Hidden input untuk ID Interaksi -->
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">

    <!-- Jadwal Survey -->
    <div class="form-group">
        <label>Jadwal Survey</label>
        <div class="input-group mb-2">
            <input type="text" class="form-control" id="jadwal_survey" name="jadwal_survey"
                placeholder="Pilih tanggal dan waktu..." required>
            <div class="input-group-append">
                <span class="input-group-text">WIB</span>
            </div>
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
     <div class="modal-footer">
        <button type="submit" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary btn-close-modal">Batal</button>
    </div>
</form>
<style>
#crudModal .modal-body {
    padding: 20px 25px;
}

/* Supaya tombol kemarin & hari ini tidak menabrak input */
#crudModal .input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

#crudModal .btn-outline-primary {
    border-radius: 0;
}

/* Rapikan spacing antar elemen */
#crudModal .form-group {
    margin-bottom: 18px;
}

/* Form di dalam modal diberi ruang ke bawah */
#crudModal form {
    padding-bottom: 10px;
}

/* Tinggi minimal textarea */
#crudModal textarea {
    min-height: 90px;
}

/* Modal header biar lebih rapi */
.modal-header.bg-wallpaper-gradient {
    padding: 12px 20px;
    border-bottom: none;
    border-radius: 0.5rem 0.5rem 0 0;
}
    </style>
<script>
$(function() {
    // 1. Inisialisasi Flatpickr pada input jadwal survey
    flatpickr("#jadwal_survey", {
        enableTime: true,           // Mengaktifkan pilihan waktu
        dateFormat: "Y-m-d H:i:S",  // Format yang dikirim ke server (database)
        altInput: true,             // Membuat input visual yang berbeda untuk pengguna
        altFormat: "d-m-Y, H:i",    // Format yang dilihat oleh pengguna (dd-mm-yyyy, hh:mm)
        time_24hr: true,            // Menggunakan format waktu 24 jam
        defaultDate: new Date(),    // Default tanggal dan waktu saat ini
        minuteIncrement: 1,
    });
  // Tutup modal tanpa reload
    $(document).on('click', '.btn-close-modal', function() {
        $('#crudModal').modal('hide');
    });
    // 2. Submit form menggunakan AJAX
    $("#form-create-survey").submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append("interaksi_id", $("#interaksi_id").val());

        // TIDAK PERLU LAGI PARSING TANGGAL MANUAL!
        // Flatpickr sudah otomatis menyimpan format yang benar (Y-m-d H:i:s)
        // di input `jadwal_survey` yang asli.

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
                    if (res.html) {
                        $("#survey-tabel-body").html(res.html);
                    }
                    $("a[title='Tambah Survey']").hide();
                } else {
                    Swal.fire("Gagal", res.message || "Terjadi kesalahan.", "error");
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
});
</script>