@empty($rincian)
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

@isset($rincian)
<form action="{{ route('rincian.update', $rincian->rincian_id) }}" method="POST" id="form-edit-rincian" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Rincian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

    <input type="hidden" name="rincian_id" value="{{ $rincian->rincian_id }}">
    <input type="hidden" name="interaksi_id" id="interaksi_id" value="{{ $rincian->interaksi_id }}">

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
                                {{ $rincian->produk_id == $prd->produk_id ? 'selected' : '' }}>
                            {{ $prd->kategori->kategori_nama ?? $prd->kategori_nama }} - {{ $prd->produk_nama }}
                        </option>
                    @endforeach
                </select>
                <small id="error-produk_id" class="text-danger"></small>
            </div>
        </div>
        <!-- Kuantitas -->
        <div class="col-md-3">
            <div class="form-group">
                <label>Kuantitas</label>
                <div class="input-group">
                    <input type="number" name="kuantitas" id="kuantitas" 
                        value="{{ $rincian->kuantitas }}" 
                        class="form-control" min="1" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="satuan-label"></span>
                    </div>
                </div>
                <small id="error-kuantitas" class="text-danger"></small>
            </div>
        </div>
    </div>

    <!-- Deskripsi -->
    <div class="form-group">
        <label>Deskripsi</label>
        <input type="text" name="deskripsi" id="deskripsi" 
               value="{{ $rincian->deskripsi }}" 
               class="form-control">
        <small id="error-deskripsi" class="text-danger"></small>
    </div>
    <!-- Status -->
    <div class="form-group">
        <label>Status</label>
        <select name="status" id="status" class="form-control" required>
            <option value="">-- Pilih Status --</option>
            <option value="hold" {{ $rincian->status == 'hold' ? 'selected' : '' }}>Hold</option>
            <option value="closing" {{ $rincian->status == 'closing' ? 'selected' : '' }}>Closing</option>
        </select>
        <small id="error-status" class="text-danger"></small>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endisset

<script>
$(document).ready(function () {
    $("#form-edit-rincian").submit(function (e) {
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

    // Saat pertama kali modal edit dibuka
    let satuanAwal = $("#produk_id").find(":selected").data("satuan") || "";
    $("#satuan-label").text(satuanAwal);

    // Update satuan setiap kali pilihan produk berubah
    $("#produk_id").on("change", function () {
        let satuan = $(this).find(":selected").data("satuan") || "";
        $("#satuan-label").text(satuan);
    });
});
</script>
