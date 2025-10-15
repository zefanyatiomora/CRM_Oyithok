<div class="modal-header">
    <h5 class="modal-title">Tambah Pemasangan/Kirim</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="form-create-pasang" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id }}">

    <div class="row">
        <div class="col-md-9">
            <div class="form-group">
                <label>Produk</label>
                <select name="produk_id" id="produk_id" class="form-control" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($produk as $prd)
                        <option value="{{ $prd->produk_id }}" data-satuan="{{ $prd->satuan }}">
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
                    <input type="number" name="kuantitas" id="kuantitas" class="form-control" min="1" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="satuan-label"></span>
                    </div>
                </div>
                <small id="error-kuantitas" class="text-danger"></small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Deskripsi</label>
        <input type="text" name="deskripsi" id="deskripsi" class="form-control">
        <small id="error-deskripsi" class="text-danger"></small>
    </div>

    <div class="form-group">
        <label>Jadwal</label>
        <div class="input-group mb-2">
            <input type="text" class="form-control" id="jadwal_pasang_kirim" name="jadwal_pasang_kirim"
                placeholder="dd-mm-yyyy, hh:mm WIB"
                value="{{ old('jadwal_pasang_kirim', \Carbon\Carbon::now()->format('d-m-Y, H:i')) . ' WIB' }}" required>
            <button type="button" class="btn btn-outline-primary" id="btn-today">Hari Ini</button>
            <button type="button" class="btn btn-outline-primary" id="btn-tomorrow">Besok</button>
        </div>
        <small id="error-jadwal" class="text-danger"></small>
    </div>

    <div class="form-group">
        <label>Alamat</label>
        <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
        <small id="error-alamat" class="text-danger"></small>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status" id="status" class="form-control" required>
            <option value="">-- Pilih Status --</option>
            @foreach ($closing as $cls)
                <option value="{{ $cls }}">{{ $cls }}</option>
            @endforeach
        </select>
        <small id="error-status" class="text-danger"></small>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
</form>

<script>
    $(function() {
        function pad2(n) {
            return n.toString().padStart(2, '0');
        }

        // Tombol Hari Ini
        $('#btn-today').click(function() {
            let now = new Date();
            $('#jadwal_pasang_kirim').val(
                `${pad2(now.getDate())}-${pad2(now.getMonth()+1)}-${now.getFullYear()}, ${pad2(now.getHours())}:${pad2(now.getMinutes())} WIB`
            );
            $('#error-jadwal').text('');
        });

        // Tombol Besok
        $('#btn-tomorrow').click(function() {
            let now = new Date();
            now.setDate(now.getDate() + 1);
            $('#jadwal_pasang_kirim').val(
                `${pad2(now.getDate())}-${pad2(now.getMonth()+1)}-${now.getFullYear()}, ${pad2(now.getHours())}:${pad2(now.getMinutes())} WIB`
            );
            $('#error-jadwal').text('');
        });

        // Submit AJAX
        $("#form-create-pasang").submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            formData.append("interaksi_id", $("#interaksi_id").val());

            // Ambil dan validasi jadwal
            let raw = $("#jadwal_pasang_kirim").val().trim();
            if (!raw) {
                $("#error-jadwal").text("Jadwal wajib diisi");
                return;
            }

            let re = /^\d{2}-\d{2}-\d{4}, \d{2}:\d{2} WIB$/;
            if (!re.test(raw)) {
                $("#error-jadwal").text("Format harus: dd-mm-yyyy, hh:mm WIB");
                return;
            }

            // Konversi ke format DB (YYYY-MM-DD HH:mm:ss)
            let [tgl, waktu] = raw.split(',');
            let [d, m, y] = tgl.trim().split('-').map(x => pad2(x));
            let [hh, min] = waktu.replace('WIB', '').trim().split(':').map(x => pad2(x));
            let iso = `${y}-${m}-${d} ${hh}:${min}:00`;
            formData.set("jadwal_pasang_kirim", iso);

            $.ajax({
                url: "{{ route('pasang.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        $("#crudModal").modal('hide');
                        let card = $("#card-pasang");
                        if (card.length && card.hasClass("collapsed-card")) {
                            try {
                                card.CardWidget('expand');
                            } catch (e) {}
                        }
                        if ($("#tbody-pasang").length) {
                            $("#tbody-pasang").html(res.html);
                        }
                    } else {
                        Swal.fire("Gagal", res.message, "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Gagal", "Terjadi kesalahan server", "error");
                    console.error(xhr.responseText);
                }
            });

        });
    });
</script>
