<div class="modal-header bg-wallpaper-gradient text-white">
    <h5 class="modal-title">Interaksi Harian</h5>
</div>
<div class="modal-body">
<form id="form-create-realtime" enctype="multipart/form-data">
    @csrf
    <!-- Hidden input untuk ID Interaksi dan ID User-->
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">
    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    <!-- Tanggal -->
    <div class="form-group">
        <label>Tanggal</label>
        <div class="input-group">
            <input type="text" class="form-control" id="tanggal" name="tanggal"
                value="{{ old('tanggal', \Carbon\Carbon::today()->format('d-m-Y')) }}" placeholder="dd-mm-yyyy"
                required>
            <button type="button" class="btn btn-outline-primary" id="btn-yesterday">Kemarin</button>
            <button type="button" class="btn btn-outline-primary" id="btn-today">Hari Ini</button>
        </div>
        <small id="error-tanggal" class="text-danger"></small>
    </div>

    <!-- Keterangan -->
    <div class="form-group">
        <label>Keterangan</label>
        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" required></textarea>
        <small id="error-keterangan" class="text-danger"></small>
    </div>

    <!-- PIC -->
    {{-- <div class="form-group">
        <label for="user_id">PIC</label>
        <select name="user_id" id="user_id" class="form-control" required>
            <option value="">-- Pilih PIC --</option>
            @foreach ($picList as $pic)
                <option value="{{ $pic->user_id }}">{{ $pic->nama }}</option>
            @endforeach
        </select>
        <small id="error-user" class="text-danger"></small>
    </div> --}}


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
$(function() {
    // Inisialisasi Flatpickr (tanpa waktu, format dd-mm-yyyy)
    const fp = flatpickr("#tanggal", {
        dateFormat: "d-m-Y",     // Format tampilan untuk user
        altInput: false,
        defaultDate: "{{ \Carbon\Carbon::today()->format('d-m-Y') }}",
        allowInput: true,
        clickOpens: true,
        locale: "id"
    });

    // Tombol "Hari Ini"
    $('#btn-today').click(function() {
        fp.setDate(new Date()); // Flatpickr API (langsung update kalender & input)
    });

    // Tombol "Kemarin"
    $('#btn-yesterday').click(function() {
        let yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        fp.setDate(yesterday);
    });
 // Tombol Batal / Close
    $(document).on('click', '.btn-close-modal', function() {
        // Tutup modal tanpa reload
        $('#crudModal').modal('hide');
    });
        $('#btn-yesterday').click(function() {
            let d = new Date();
            d.setDate(d.getDate() - 1);
            let formatted = String(d.getDate()).padStart(2, '0') + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                d.getFullYear();
            $('#tanggal').val(formatted);
        });

        $("#form-create-realtime").submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            formData.append("interaksi_id", $("#interaksi_id").val());
            let tglInput = $('#tanggal').val();
            if (tglInput) {
                let parts = tglInput.split('-');
                if (parts.length === 3) {
                    // set() supaya menimpa nilai tanggal di FormData (jika sudah ada)
                    formData.set('tanggal', `${parts[2]}-${parts[1]}-${parts[0]}`);
                }
            }
            $.ajax({
                url: "{{ route('realtime.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);

                        // Tutup modal
                        $("#crudModal").modal('hide');

                        // Update isi tabel realtime langsung tanpa reload
                        $("#realtime-tabel-container").html(res.html);
                    } else {
                        Swal.fire("Gagal", res.message ||
                            "Terjadi kesalahan saat menyimpan data.", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                    console.error("Server Error:", xhr.responseText);
                }
            });
        });
    });
</script>
