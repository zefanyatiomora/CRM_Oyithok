<div class="modal-header">
    <h5 class="modal-title">Tambah Pemasangan/Kirim</h5>
    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="form-create-pasang" enctype="multipart/form-data">
    @csrf
    <!-- Hidden input untuk ID Interaksi -->
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">


    <div class="row">
        <div class="col-md-9">
            <!-- Produk -->
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
        <div class="col-md-3">
            <!-- Kuantitas -->
            <div class="form-group">
                <label>Kuantitas</label>
                <div class="input-group">
                    <input type="number" name="kuantitas" id="kuantitas" class="form-control" min="1" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="satuan-label">
                        </span>
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
    <!-- Jadwal Pasang -->
    <div class="form-group">
        <label>Jadwal</label>
        <div class="input-group">
            <input type="date" class="form-control" id="jadwal_pasang_kirim" name="jadwal_pasang_kirim"
                value="{{ old('jadwal_pasang_kirim', \Carbon\Carbon::today()->format('Y-m-d')) }}" required>
            <button type="button" class="btn btn-outline-primary" id="btn-today">Hari Ini</button>
            <button type="button" class="btn btn-outline-primary" id="btn-tomorrow">Besok</button>
        </div>
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
$(function () {
    // Tombol "Hari Ini" dan "Besok"
    $('#btn-today').click(function() {
        let today = new Date().toISOString().split('T')[0];
        $('#jadwal_pasang_kirim').val(today);
    });

    $('#btn-tomorrow').click(function() {
        let d = new Date();
        d.setDate(d.getDate() + 1);
        let tomorrow = d.toISOString().split('T')[0];
        $('#jadwal_pasang_kirim').val(tomorrow);
    });
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

                // $("#form-create-pasang").hide();
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
