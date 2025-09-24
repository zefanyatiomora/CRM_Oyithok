<div class="modal-header">
    <h5 class="modal-title">Tambah Rincian</h5>
    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="form-create-rincian" enctype="multipart/form-data">
    @csrf
    <!-- Hidden input untuk ID Interaksi -->
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">


    <div class="row">
        <!-- Produk -->
        <div class="col-md-9">
            <div class="form-group">
                <label>Produk</label>
                <select name="produk_id" id="produk_id" class="form-control" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produk as $prd)
                        <option value="{{ $prd->produk_id }}" data-satuan="{{ $prd->satuan }}">
                            {{ $prd->kategori->kategori_nama ?? $prd->kategori_nama }} - {{ $prd->produk_nama }}</option>
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
                    <input type="number" name="kuantitas" id="kuantitas" class="form-control" min="1" required>
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
        <input type="text" name="deskripsi" id="deskripsi" class="form-control">
        <small id="error-deskripsi" class="text-danger"></small>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-success">Simpan</button>

</form>

<script>
$(document).ready(function () {
    // Submit Form dengan AJAX
    $("#form-create-rincian").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append("interaksi_id", $("#interaksi_id").val());

        $.ajax({
            url: "{{ route('rincian.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success('Rincian berhasil disimpan');
                tableRekap.ajax.reload(null, false);

                let interaksiId = $("#interaksi_id").val();
                $("#myModal").load("{{ url('rekap') }}/" + interaksiId + "/show_ajax");

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
