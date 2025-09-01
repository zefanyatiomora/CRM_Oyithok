<form id="form-create-rincian" enctype="multipart/form-data">
    @csrf
    <!-- Hidden input untuk ID Interaksi -->
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">


    <!-- Produk -->
    <div class="form-group">
        <label>Produk</label>
        <select name="produk_id" id="produk_id" class="form-control" required>
            <option value="">-- Pilih Produk --</option>
            @foreach($produk as $prd)
                <option value="{{ $prd->produk_id }}">{{ $prd->produk_nama }}</option>
            @endforeach
        </select>
        <small id="error-produk_id" class="text-danger"></small>
    </div>

    <!-- Satuan -->
    <div class="form-group">
        <label>Satuan</label>
        <select name="satuan" id="satuan" class="form-control" required>
            <option value="">-- Pilih Satuan --</option>
            <option value="pcs">PCS</option>
            <option value="roll">Roll</option>
            <option value="box">Box</option>
            <option value="box">Meter</option>
        </select>
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

    <!-- Submit Button -->
    <button type="submit" class="btn btn-success">Simpan</button>

</form>

<script>
$(document).ready(function () {
    // Tambah baris baru
    $("#add-row").click(function () {
        let newRow = `
            <tr>
                <td>
                    <select name="produk_id[]" class="form-control" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produk as $prd)
                            <option value="{{ $prd->produk_id }}">{{ $prd->produk_nama }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="satuan[]" class="form-control" required>
                        <option value="pcs">PCS</option>
                        <option value="roll">Roll</option>
                        <option value="box">Box</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="qty[]" class="form-control" min="1" required>
                </td>
                <td>
                    <input type="text" name="keterangan[]" class="form-control">
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-row">Hapus</button>
                </td>
            </tr>`;
        $("#rincian-body").append(newRow);
    });

    // Hapus baris
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr").remove();
    });

    // Submit Form dengan AJAX
    $("#form-create-rincian").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        // pastikan interaksi_id terkirim
        formData.append("interaksi_id", $("#interaksi_id").val());

        $.ajax({
            url: "{{ route('rincian.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success('Status nya woi berhasil disimpan');
                // reload datatable utama di index.blade.php
                tableRekap.ajax.reload(null, false); // false = biar tetap di halaman yang sama
                // Optional: reload ulang isi modal juga kalau mau lihat status terbarunya
                let interaksiId = $("#interaksi_id").val();
                $("#myModal").load("{{ url('rekap') }}/" + interaksiId + "/show_ajax");

                // Sembunyikan form
                $("#form-create-rincian").hide();

            },

            error: function (xhr) {
                Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                console.error("Server Error:", xhr.responseText);
            }
        });
    });
});
</script>
