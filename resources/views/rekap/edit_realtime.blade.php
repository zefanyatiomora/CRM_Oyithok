<div class="modal-header bg-wallpaper-gradient text-white">
    <h5 class="modal-title">Edit Interaksi Harian</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" class="text-white">&times;</span>
    </button>
</div>
<div class="modal-body">
<form id="form-update-realtime">
    @csrf
    @method('PUT')
    <input type="hidden" name="realtime_id" value="{{ $realtime->realtime_id }}">
    <input type="hidden" name="user_id" value="{{ $realtime->user_id }}">
    <!-- Tanggal -->
    <div class="form-group">
        <label>Tanggal</label>
        <input type="text" class="form-control" id="edit-tanggal" name="tanggal"
            value="{{ \Carbon\Carbon::parse($realtime->tanggal)->format('d-m-Y') }}" required>
    </div>

    <!-- Keterangan -->
    <div class="form-group">
        <label>Keterangan</label>
        <textarea class="form-control" id="edit-keterangan" name="keterangan" rows="3" required>{{ $realtime->keterangan }}</textarea>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn bg-wallpaper-gradient text-white border-0 fw-bold" style="border-radius: 0.35rem;">Simpan</button>
        <button type="button" class="btn btn-secondary btn-close-modal">Batal</button>
    </div>
    </div>
</form>
<style>
    /* Modal body diberi padding */
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
$("#form-update-realtime").submit(function(e) {
    e.preventDefault();

    let id = $("input[name=realtime_id]").val();
    let formData = new FormData(this);

    // override method PUT
    formData.set('_method', 'PUT');

    // Format tanggal dd-mm-yyyy -> yyyy-mm-dd
    let parts = $('#edit-tanggal').val().split('-');
    if (parts.length === 3) {
        formData.set('tanggal', `${parts[2]}-${parts[1]}-${parts[0]}`);
    }

    // Build URL menggunakan route update: /rekap/realtime/update/{id}
    // NOTE: blade akan mengganti bagian REPLACE_ID, lalu kita replace dengan id JS
    let updateUrlTemplate = "{{ route('realtime.update', 'REPLACE_ID') }}";
    let updateUrl = updateUrlTemplate.replace('REPLACE_ID', id);
     // Tombol Batal / Close
    $(document).on('click', '.btn-close-modal', function() {
        // Tutup modal tanpa reload
        $('#crudModal').modal('hide');
    });
    $.ajax({
        url: updateUrl,
        type: "POST", // kirim POST dengan _method=PUT
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.status === 'success') {
                toastr.success(res.message || 'Berhasil update');
                $("#realtime-tabel-container").html(res.html);
                $("#crudModal").modal("hide");
            } else {
                Swal.fire("Gagal", res.message || "Terjadi kesalahan saat menyimpan.", "error");
            }
        },
        error: function(xhr) {
            console.error("Update Error:", xhr.responseText);

            // Jika validation failed (422), tampilkan pesan dari server
            if (xhr.status === 422 && xhr.responseJSON) {
                let errors = xhr.responseJSON.errors || {};
                let messages = [];
                Object.keys(errors).forEach(function(k) {
                    messages.push(errors[k].join(', '));
                });
                Swal.fire("Validasi Gagal", messages.join('<br>'), "warning");
                return;
            }

            // Pesan fallback
            Swal.fire("Gagal", "Terjadi kesalahan saat update. Cek laravel.log.", "error");
        }
    });
});
</script>
