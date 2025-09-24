@empty($pasang)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Data yang Anda cari tidak ditemukan</h5>
            </div>
        </div>
    </div>
</div>
@endempty

@isset($pasang)
<form action="{{ route('pasang.update', $pasang->pasangkirim_id) }}" method="POST" id="form-edit-pasang" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data pasang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

    <input type="hidden" name="pasangkirim_id" value="{{ $pasang->pasangkirim_id }}">
    <input type="hidden" name="interaksi_id" id="interaksi_id" value="{{ $pasang->interaksi_id }}">

    <div class="row">
    <!-- Produk -->
        <div class="col-md-9">
            <div class="form-group">
                <label>Produk</label>
                <select name="produk_id" id="produk_id" class="form-control" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produk as $prd)
                        <option value="{{ $prd->produk_id }}" 
                                data-satuan="{{ $prd->satuan }}"
                                {{ $pasang->produk_id == $prd->produk_id ? 'selected' : '' }}>
                            {{ $prd->kategori->kategori_nama ?? $prd->kategori_nama }} - {{ $prd->produk_nama }}
                        </option>
                    @endforeach
                </select>
                <small id="error-produk_id" class="text-danger"></small>
            </div>
        </div>
        <div class="col-md-3">
            <!-- Kuantitas -->
            <div class="form-group">
                <label>Kuantitas</label>
                <div class="input-group">
                    <input type="number" name="kuantitas" id="kuantitas" 
                        value="{{ $pasang->kuantitas }}" 
                        class="form-control" min="1" required>
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
    <div class="row">
        <!-- Deskripsi -->
        <div class="col-md-6">
            <div class="form-group">
                <label>Deskripsi</label>
                <input type="text" name="deskripsi" id="deskripsi" 
                value="{{ $pasang->deskripsi }}" 
                class="form-control">
                <small id="error-deskripsi" class="text-danger"></small>
            </div>
        </div>
        <!-- Status -->
        <div class="col-md-6">
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
        </div>
    </div>
    <div class="row">
        <!-- Jadwal -->
        <div class="col-md-6">
            <div class="form-group">
                <label>Jadwal</label>
                <div class="input-group">
                    <input type="date" class="form-control" id="jadwal_pasang_kirim" name="jadwal_pasang_kirim"
                        value="{{ $pasang->jadwal_pasang_kirim ? \Carbon\Carbon::parse($pasang->jadwal_pasang_kirim)->format('Y-m-d') : '' }}">
                </div>
                <small id="error-jadwal" class="text-danger"></small>
            </div>
        </div>
        <!-- Alamat -->
        <div class="col-md-6">
            <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat" id="alamat" 
                    value="{{ $pasang->alamat }}" 
                    class="form-control">
                <small id="error-alamat" class="text-danger"></small>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endisset

<script>
$(document).ready(function () {
    $("#form-edit-pasang").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let actionUrl = $(this).attr("action");

        // Ambil interaksi_id langsung dari formData
        let interaksiId = formData.get("interaksi_id");

        $.ajax({
            url: actionUrl,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.message);

                // reload datatable
                tableRekap.ajax.reload(null, false);
                let interaksiId = $("#interaksi_id").val();

                // reload isi modal
                $("#myModal").load("{{ url('rekap') }}/" + interaksiId + "/show_ajax");

                // optionally, bisa hide form edit
                $("#crudModal").modal('hide');
            },
            error: function (xhr) {
                Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                console.error("Server Error:", xhr.responseText);
            }
        });
    });

    // Set satuan sesuai produk yang sudah terpilih (saat edit dibuka)
    let satuanAwal = $("#produk_id").find(":selected").data("satuan") || "";
    $("#satuan-label").text(satuanAwal);

    // Update satuan setiap kali pilihan produk berubah
    $("#produk_id").on("change", function () {
        let satuan = $(this).find(":selected").data("satuan") || "";
        $("#satuan-label").text(satuan);
    }); 
});
</script>
