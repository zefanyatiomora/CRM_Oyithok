<form id="form-create-pasang" enctype="multipart/form-data">
    @csrf
    <!-- Hidden input untuk ID Interaksi -->
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">


    <!-- Produk -->
    <div class="form-group">
        <label>Produk</label>
        <select name="produk_id" id="produk_id" class="form-control" required>
            <option value="">-- Pilih Produk --</option>
            @foreach($produk as $prd)
                <option value="{{ $prd->produk_id }}" data-satuan="{{ $prd->satuan }}">{{ $prd->produk_nama }}</option>
            @endforeach
        </select>
        <small id="error-produk_id" class="text-danger"></small>
    </div>

    <!-- Satuan -->
    <div class="form-group">
        <label>Satuan</label>
        <input type="text" name="satuan" id="satuan" class="form-control" readonly>
        <small id="error-satuan" class="text-danger"></small>
    </div>

    <!-- Kuantitas -->
    <div class="form-group">
        <label>Kuantitas</label>
        <input type="number" name="kuantitas" id="kuantitas" class="form-control" min="1" required>
        <small id="error-kuantitas" class="text-danger"></small>
    </div>

    <!-- Deskripsi -->
    <div class="form-group">
        <label>Deskripsi</label>
        <input type="text" name="deskripsi" id="deskripsi" class="form-control">
        <small id="error-deskripsi" class="text-danger"></small>
    </div>
    <!-- Jadwal Pasang -->
    <div class="form-group">
        <label>Jadwal</label>
        <input type="datetime-local" name="jadwal_pasang_kirim" id="jadwal_pasang_kirim" class="form-control" required>
        <small id="error-jadwal" class="text-danger"></small>
    </div>

    <!-- Alamat Pasang -->
    <div class="form-group">
        <label>Alamat</label>
        <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
        <small id="error-alamat" class="text-danger"></small>
    </div>
    <!-- Status -->
    <div class="form-group">
        <label>Status</label>
        <select name="status" id="status" class="form-control" required>
            <option value="">-- Pilih Status --</option>
            @foreach($closing as $cls)
                <option value="{{ $cls }}">{{ $cls }}</option>
            @endforeach
        </select>
        <small id="error-status" class="text-danger"></small>
    </div>


    <!-- Submit Button -->
    <button type="submit" class="btn btn-success">Simpan</button>

</form>

<script>
$(document).ready(function () {
    // Submit Form dengan AJAX
    $("#form-create-pasang").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append("interaksi_id", $("#interaksi_id").val());

        $.ajax({
            url: "{{ route('pasang.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success('Pasang berhasil disimpan');
                tableRekap.ajax.reload(null, false);

                let interaksiId = $("#interaksi_id").val();
                $("#myModal").load("{{ url('rekap') }}/" + interaksiId + "/show_ajax");

                $("#form-create-pasang").hide();
            },
            error: function (xhr) {
                Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                console.error("Server Error:", xhr.responseText);
            }
        });
    });

    // Isi otomatis satuan sesuai produk
    $("#produk_id").on("change", function () {
        let satuan = $(this).find(":selected").data("satuan") || "";
        $("#satuan").val(satuan);
    });
});
</script>
