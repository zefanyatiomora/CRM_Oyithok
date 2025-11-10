@empty($rincian)
{{-- =================================================================
-- KONDISI JIKA DATA TIDAK DITEMUKAN (mengikuti template edit_pasang)
================================================================= --}}
<div class="modal-header bg-danger text-white">
    <h5 class="modal-title">Kesalahan</h5>
    {{-- Tombol 'Back to Master' --}}
    <button type="button" class="close btn-back-to-master" aria-label="Close">
        <span aria-hidden="true" class="text-white">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="alert alert-danger m-0">
        <h5><i class="icon fas fa-ban"></i> Data yang Anda cari tidak ditemukan</h5>
        <p>Data Rincian mungkin telah dihapus.</p>
    </div>
</div>
@endempty


@isset($rincian)
{{-- =================================================================
-- KONDISI JIKA DATA DITEMUKAN (FORM EDIT) (mengikuti template edit_pasang)
================================================================= --}}

{{-- Header diganti jadi bg-wallpaper-gradient --}}
<div class="modal-header bg-wallpaper-gradient text-white">
    <h5 class="modal-title">Edit Data Rincian</h5>
    {{-- Tombol 'Back to Master' --}}
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

{{-- Form dipindahkan ke dalam modal-body --}}
<div class="modal-body">
    <form id="form-edit-rincian" action="{{ route('rincian.update', $rincian->rincian_id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Input tersembunyi --}}
        <input type="hidden" name="rincian_id" value="{{ $rincian->rincian_id }}">
        <input type="hidden" name="interaksi_id" id="interaksi_id" value="{{ $rincian->interaksi_id }}">

        {{-- Layout form disamakan (row/col) --}}
        <div class="row">
            <div class="col-md-9">
                <div class="form-group">
                    <label>Produk</label>
                    <select name="produk_id" id="produk_id" class="form-control" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($produk as $prd)
                            <option value="{{ $prd->produk_id }}" data-satuan="{{ $prd->satuan }}"
                                {{ $rincian->produk_id == $prd->produk_id ? 'selected' : '' }}>
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
                        <input type="number" name="kuantitas" id="kuantitas" value="{{ $rincian->kuantitas }}"
                            class="form-control" min="1" required>
                        <div class="input-group-append">
                            <span class="input-group-text" id="satuan-label">
                                {{-- Menambahkan satuan yang sudah ada --}}
                                {{ $rincian->produk->satuan ?? '' }}
                            </span>
                        </div>
                    </div>
                    <small id="error-kuantitas" class="text-danger"></small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <input type="text" name="deskripsi" id="deskripsi" value="{{ $rincian->deskripsi }}"
                class="form-control">
            <small id="error-deskripsi" class="text-danger"></small>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="hold" {{ $rincian->status == 'hold' ? 'selected' : '' }}>Hold</option>
                <option value="closing" {{ $rincian->status == 'closing' ? 'selected' : '' }}>Closing</option>
            </select>
            <small id="error-status" class="text-danger"></small>
        </div>
    <div class="modal-footer">
    <button type="submit" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary btn-close-modal">Batal</button>
    </div>
    </div>
</form>
</div> {{-- end modal-body --}}


{{-- CSS di-copy dari edit_pasang --}}
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

        // === Submit Form Edit (Logika asli dari edit_rincian) ===
        $("#form-edit-rincian").submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            let actionUrl = $(this).attr("action");
            let interaksiId = formData.get("interaksi_id"); // Ambil ID Interaksi

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
success: function(response) {
    toastr.success(response.message);

    // Tutup modal edit
    $("#crudModal").modal('hide');

    // Update tabel rincian di show_ajax tanpa reload seluruh card
    if (response.html) {
        $(".table-rincian tbody").html(response.html);
    } else {
        // fallback jika controller tidak kirim html, reload tampilan rincian aja
        $("#myModal").find(".table-rincian tbody").load("{{ url('rekap') }}/" + interaksiId + " #myModal .table-rincian tbody > *");
    }
},
                error: function(xhr) {
                    Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                    console.error("Server Error:", xhr.responseText);
                }
            });
        });

        // === Update Satuan Label (Logika asli dari edit_rincian) ===
        let satuanAwal = $("#produk_id").find(":selected").data("satuan") || "";
        $("#satuan-label").text(satuanAwal);
        $("#produk_id").on("change", function() {
            let satuan = $(this).find(":selected").data("satuan") || "";
            $("#satuan-label").text(satuan);
        });

            $(document).on('click', '.btn-close-modal', function() {
        // Tutup modal tanpa reload
        $('#crudModal').modal('hide');
    });
        {{-- ====================================================== --}}
        {{-- Handler Tombol 'Close' (X) (di-copy dari edit_pasang) --}}
        {{-- ====================================================== --}}
        $('.btn-back-to-master').on('click', function(e) {
            e.preventDefault(); 

            // Ambil ID Interaksi dari form edit_rincian
            let interaksiId = $('#form-edit-rincian input[name="interaksi_id"]').val();
            
            let masterViewUrl = "{{ url('rekap') }}/" + interaksiId + "/show_ajax";
            
            // Container tempat modal-dialog ini dimuat (asumsi #crudModal .modal-content)
            let dialogContainer = $(this).closest('.modal-content');
            
            // Tampilkan spinner loading
            let loadingHtml = `
                <div class="modal-body text-center p-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3 mb-0">Memuat detail...</p>
                </div>`;
            $(dialogContainer).html(loadingHtml);

            // Muat ulang konten 'show_ajax' ke dalam container
            $(dialogContainer).load(masterViewUrl, function(response, status, xhr) {
                if (status == "error") {
                    let errorHtml = `
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Error</h5>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger m-0">
                                <h5>Gagal memuat data</h5>
                                <p>Terjadi kesalahan saat kembali ke halaman detail.</p>
                                <small>${xhr.status} ${xhr.statusText}</small>
                            </div>
                        </div>`;
                    $(dialogContainer).html(errorHtml);
                    console.error("Gagal memuat master view:", xhr.statusText);
                }
            });
        });

    });
</script>
@endisset