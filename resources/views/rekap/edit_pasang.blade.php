@empty($pasang)
{{-- =================================================================
-- KONDISI JIKA DATA TIDAK DITEMUKAN
================================================================= --}}
<div class="modal-header bg-danger text-white">
    <h5 class="modal-title">Kesalahan</h5>
    {{-- {{-- PERUBAHAN 1: Hapus data-dismiss, ganti dengan class --}}
    <button type="button" class="close btn-back-to-master" aria-label="Close">
        <span aria-hidden="true" class="text-white">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="alert alert-danger m-0">
        <h5><i class="icon fas fa-ban"></i> Data yang Anda cari tidak ditemukan</h5>
        <p>Data Pemasangan/Kirim mungkin telah dihapus.</p>
    </div>
</div>
@endempty


@isset($pasang)
{{-- =================================================================
-- KONDISI JIKA DATA DITEMUKAN (FORM EDIT)
================================================================= --}}
        <div class="modal-header bg-wallpaper-gradient text-white">
            <h5 class="modal-title">Edit Pemasangan/Kirim</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <form id="form-edit-pasang" action="{{ route('pasang.update', $pasang->pasangkirim_id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Input tersembunyi --}}
                <input type="hidden" name="pasangkirim_id" value="{{ $pasang->pasangkirim_id }}">
                <input type="hidden" name="interaksi_id" id="interaksi_id" value="{{ $pasang->interaksi_id }}">

                {{-- Layout form --}}
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Produk</label>
                            <select name="produk_id" id="produk_id" class="form-control" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($produk as $prd)
                                    <option value="{{ $prd->produk_id }}" data-satuan="{{ $prd->satuan }}"
                                        {{ $pasang->produk_id == $prd->produk_id ? 'selected' : '' }}>
                                        {{ $prd->kategori->kategori_nama ?? $prd->kategori_nama }} -
                                        {{ $prd->produk_nama }}
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
                                <input type="number" name="kuantitas" id="kuantitas" class="form-control" min="1"
                                    required value="{{ $pasang->kuantitas }}">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="satuan-label">
                                        {{ $pasang->produk->satuan ?? '' }}
                                    </span>
                                </div>
                            </div>
                            <small id="error-kuantitas" class="text-danger"></small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <input type="text" name="deskripsi" id="deskripsi" class="form-control"
                        value="{{ $pasang->deskripsi }}">
                    <small id="error-deskripsi" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Jadwal</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="jadwal_pasang_kirim" name="jadwal_pasang_kirim"
                            placeholder="Pilih tanggal dan waktu..." required
                            value="{{ $pasang->jadwal_pasang_kirim ? \Carbon\Carbon::parse($pasang->jadwal_pasang_kirim)->format('Y-m-d H:i:S') : '' }}">
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
                    <textarea name="alamat" id="alamat" class="form-control" rows="3"
                        required>{{ $pasang->alamat }}</textarea>
                    <small id="error-alamat" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="closing all" {{ strtolower($pasang->status) == 'closing all' ? 'selected' : '' }}>Closing All</option>
                        <option value="closing produk" {{ strtolower($pasang->status) == 'closing produk' ? 'selected' : '' }}>Closing Produk</option>
                        <option value="closing pasang" {{ strtolower($pasang->status) == 'closing pasang' ? 'selected' : '' }}>Closing Pasang</option>
                    </select>
                    <small id="error-status" class="text-danger"></small>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div> {{-- end modal-body --}}


{{-- CSS yang sama dari create_pasang --}}
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


{{-- JavaScript Gabungan --}}
<script>
    $(document).ready(function() {

        // === Inisialisasi Flatpickr ===
        const fp = flatpickr("#jadwal_pasang_kirim", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d-m-Y, H:i",
            time_24hr: true,
            minuteIncrement: 1,
        });

        $('#btn-today').click(function() { fp.setDate(new Date()); });
        $('#btn-tomorrow').click(function() {
            let tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            fp.setDate(tomorrow);
        });
        
        // === Submit Form Edit via AJAX ===
        $("#form-edit-pasang").submit(function(e) {
            e.preventDefault();
            // ... (logika submit Anda yang sudah ada) ...
            let formData = new FormData(this);
            let actionUrl = $(this).attr("action");

            if (!$("#jadwal_pasang_kirim").val()) {
                $("#error-jadwal").text("Jadwal wajib diisi");
                return;
            } else {
                $("#error-jadwal").text("");
            }

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        $("#crudModal").modal('hide'); // Ini akan menutup modal setelah sukses
                        $("#tbody-pasang").html(res.html);
                        $("#invoice-buttons-container").html(res.invoice_buttons);
                    }
                },
                error: function(xhr) {
                    Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                    console.error("Server Error:", xhr.responseText);
                }
            });
        });

        // === Update Satuan Label ===
        let satuanAwal = $("#produk_id").find(":selected").data("satuan") || "";
        $("#satuan-label").text(satuanAwal);
        $("#produk_id").on("change", function() {
            let satuan = $(this).find(":selected").data("satuan") || "";
            $("#satuan-label").text(satuan);
        });


        {{-- ====================================================== --}}
        {{-- PERUBAHAN 2: JavaScript Handler untuk Tombol 'Close' (X) --}}
        {{-- ====================================================== --}}
        $('.btn-back-to-master').on('click', function(e) {
            e.preventDefault(); // Mencegah perilaku default (jika ada)

            // Ambil ID Interaksi dari input hidden di dalam form
            let interaksiId = $('#form-edit-pasang input[name="interaksi_id"]').val();
            
            // Definisikan URL untuk memuat 'show_ajax' (master view)
            let masterViewUrl = "{{ url('rekap') }}/" + interaksiId + "/show_ajax";

            // Cari container tempat '.modal-dialog' ini dimuat
            // Ini adalah 'parent' dari div #modal-master
            let dialogContainer = $('#modal-master').parent();

            // Tampilkan spinner loading saat memuat
            let loadingHtml = `
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                            <p class="mt-3 mb-0">Memuat detail...</p>
                        </div>
                    </div>
                </div>`;
            $(dialogContainer).html(loadingHtml);

            // Muat ulang konten 'show_ajax' ke dalam container
            $(dialogContainer).load(masterViewUrl, function(response, status, xhr) {
                if (status == "error") {
                    // Jika gagal, tampilkan pesan error
                    let errorHtml = `
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Error</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger m-0">
                                        <h5>Gagal memuat data</h5>
                                        <p>Terjadi kesalahan saat kembali ke halaman detail.</p>
                                        <small>${xhr.status} ${xhr.statusText}</small>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    $(dialogContainer).html(errorHtml);
                    console.error("Gagal memuat master view:", xhr.statusText);
                }
                // Jika sukses, 'load()' akan otomatis mengganti konten.
            });
        });

    });
</script>
@endisset