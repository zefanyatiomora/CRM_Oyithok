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
            </div>
            <div class="modal-body">

    <input type="hidden" name="rincian_id" value="{{ $rincian->rincian_id }}">
    <input type="hidden" name="interaksi_id" value="{{ $rincian->interaksi_id }}">

    <!-- Produk -->
    <div class="form-group">
        <label>Produk</label>
        <select name="produk_id" id="produk_id" class="form-control" required>
            <option value="">-- Pilih Produk --</option>
            @foreach($produk as $prd)
                <option value="{{ $prd->produk_id }}" 
                    {{ $rincian->produk_id == $prd->produk_id ? 'selected' : '' }}>
                    {{ $prd->produk_nama }}
                </option>
            @endforeach
        </select>
        <small id="error-produk_id" class="text-danger"></small>
    </div>

    <!-- Satuan -->
    <div class="form-group">
        <label>Satuan</label>
        <select name="satuan" id="satuan" class="form-control" required>
            <option value="">-- Pilih Satuan --</option>
            <option value="pcs" {{ $rincian->satuan == 'pcs' ? 'selected' : '' }}>PCS</option>
            <option value="roll" {{ $rincian->satuan == 'roll' ? 'selected' : '' }}>Roll</option>
            <option value="box" {{ $rincian->satuan == 'box' ? 'selected' : '' }}>Box</option>
            <option value="meter" {{ $rincian->satuan == 'meter' ? 'selected' : '' }}>Meter</option>
        </select>
        <small id="error-satuan" class="text-danger"></small>
    </div>

    <!-- Kuantitas -->
    <div class="form-group">
        <label>Kuantitas</label>
        <input type="number" name="kuantitas" id="kuantitas" 
               value="{{ $rincian->kuantitas }}" 
               class="form-control" min="1" required>
        <small id="error-kuantitas" class="text-danger"></small>
    </div>

    <!-- Deskripsi -->
    <div class="form-group">
        <label>Deskripsi</label>
        <input type="text" name="deskripsi" id="deskripsi" 
               value="{{ $rincian->deskripsi }}" 
               class="form-control">
        <small id="error-deskripsi" class="text-danger"></small>
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

        $.ajax({
            url: actionUrl,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire("Sukses", response.message, "success").then(() => location.reload());
            },
            error: function (xhr) {
                Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                console.error("Server Error:", xhr.responseText);
            }
        });
    });
});
</script>
