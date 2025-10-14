<div class="modal-header">
    <h5 class="modal-title">Interaksi Harian</h5>
    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="form-create-realtime" enctype="multipart/form-data">
    @csrf
    <!-- Hidden input untuk ID Interaksi dan ID User-->
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">
    <input type="hidden" name="user_id" value="{{ auth()->id() }}"> 

    <!-- Tanggal -->
    <div class="form-group">
        <label>Tanggal</label>
        <div class="input-group">
            <input type="date" class="form-control" id="tanggal" name="tanggal"
                value="{{ old('tanggal', \Carbon\Carbon::today()->format('d-m-Y')) }}" required>
                <button type="button" class="btn btn-outline-primary" id="btn-yesterday">Kemarin</button>
                <button type="button" class="btn btn-outline-primary" id="btn-today">Hari Ini</button>
        </div>
        <small id="error-tanggal" class="text-danger"></small>
    </div>

    <!-- Keterangan -->
    <div class="form-group">
        <label>Keterangan</label>
        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" required></textarea>
        <small id="error-keterangan" class="text-danger"></small>
    </div>

    <!-- PIC -->
    {{-- <div class="form-group">
        <label for="user_id">PIC</label>
        <select name="user_id" id="user_id" class="form-control" required>
            <option value="">-- Pilih PIC --</option>
            @foreach($picList as $pic)
                <option value="{{ $pic->user_id }}">{{ $pic->nama }}</option>
            @endforeach
        </select>
        <small id="error-user" class="text-danger"></small>
    </div> --}}


    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary"
            data-dismiss="modal">Batal</button>
    </div>
</form>


<script>
$(function () {
    // Tombol "Hari Ini" dan "Besok"
    $('#btn-today').click(function() {
        let today = new Date().toISOString().split('T')[0];
        $('#tanggal').val(today);
    });

    $('#btn-yesterday').click(function() {
        let d = new Date();
        d.setDate(d.getDate() - 1);
        let yesterday = d.toISOString().split('T')[0];
        $('#tanggal').val(yesterday);
    });
    // Submit Form dengan AJAX
    $("#form-create-realtime").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append("interaksi_id", $("#interaksi_id").val());

        $.ajax({
            url: "{{ route('realtime.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success('Record Realtime berhasil disimpan');
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

});
</script>
