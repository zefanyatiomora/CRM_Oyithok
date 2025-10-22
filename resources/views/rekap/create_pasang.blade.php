<div class="modal-header bg-wallpaper-gradient text-white">
    <h5 class="modal-title">Tambah Pemasangan/Kirim</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" class="text-white">&times;</span>
    </button>
</div>
<form id="form-create-pasang" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">

    <div class="row">
        <div class="col-md-9">
            <div class="form-group">
                <label>Produk</label>
                <select name="produk_id" id="produk_id" class="form-control" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($produk as $prd)
                        <option value="{{ $prd->produk_id }}" data-satuan="{{ $prd->satuan }}">
                            {{ $prd->kategori->kategori_nama ?? $prd->kategori_nama }} - {{ $prd->produk_nama }}
                        </option>
                    @endforeach
                </select>
                <small id="error-produk_id" class="text-danger"></small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Kuantitas</label>
                <div class="input-group">
                    <input type="number" name="kuantitas" id="kuantitas" class="form-control" min="1" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="satuan-label"></span>
                    </div>
                </div>
                <small id="error-kuantitas" class="text-danger"></small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Deskripsi</label>
        <input type="text" name="deskripsi" id="deskripsi" class="form-control">
        <small id="error-deskripsi" class="text-danger"></small>
    </div>

    <div class="form-group">
        <label>Jadwal</label>
        <div class="input-group mb-2">
            <input type="text" class="form-control" id="jadwal_pasang_kirim" name="jadwal_pasang_kirim"
                placeholder="Pilih tanggal dan waktu..." required>
            <div class="input-group-append">
                <span class="input-group-text">WIB</span>
            </div>
            <button type="button" class="btn btn-outline-primary" id="btn-today">Hari Ini</button>
            <button type="button" class="btn btn-outline-primary" id="btn-tomorrow">Besok</button>
        </div>
        <small id="error-jadwal" class="text-danger"></small>
    </div>
    <div class="form-group">
        <label>Alamat</label>
        <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
        <small id="error-alamat" class="text-danger"></small>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status" id="status" class="form-control" required>
            <option value="">-- Pilih Status --</option>
            @foreach ($closing as $cls)
                <option value="{{ $cls }}">{{ $cls }}</option>
            @endforeach
        </select>
        <small id="error-status" class="text-danger"></small>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
</form>

<script>
$(function() {
    // Inisialisasi Flatpickr pada input jadwal
    const fp = flatpickr("#jadwal_pasang_kirim", {
        enableTime: true,        // Mengaktifkan pilihan waktu
        dateFormat: "Y-m-d H:i:S", // Format yang dikirim ke server (database)
        altInput: true,          // Membuat input visual yang berbeda
        altFormat: "d-m-Y, H:i", // Format yang dilihat oleh pengguna
        time_24hr: true,         // Format waktu 24 jam
        defaultDate: "today",    // Default tanggal hari ini
        minuteIncrement: 1,
    });

    // Tombol Hari Ini
    $('#btn-today').click(function() {
        fp.setDate(new Date()); // Gunakan API Flatpickr untuk set tanggal
    });

    // Tombol Besok
    $('#btn-tomorrow').click(function() {
        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        fp.setDate(tomorrow); // Gunakan API Flatpickr untuk set tanggal
    });


    // Submit AJAX (Sekarang JAUH LEBIH SIMPEL)
    $("#form-create-pasang").submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append("interaksi_id", $("#interaksi_id").val());
        
        // Validasi tidak perlu lagi parsing dan re-format tanggal!
        // Flatpickr sudah menyimpannya dalam format yang benar di input asli.
        if (!$("#jadwal_pasang_kirim").val()) {
            $("#error-jadwal").text("Jadwal wajib diisi");
            return;
        }

        $.ajax({
            url: "{{ route('pasang.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    $("#crudModal").modal('hide');
                    // ... sisa kode success Anda ...
                } else {
                    Swal.fire("Gagal", res.message, "error");
                }
            },
            error: function(xhr) {
                Swal.fire("Gagal", "Terjadi kesalahan server", "error");
                console.error(xhr.responseText);
            }
        });
    });
});
</script>