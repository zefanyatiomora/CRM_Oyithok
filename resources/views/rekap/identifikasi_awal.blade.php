<form id="form-identifikasi-awal">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi_id }}">

    <div class="form-group">
        <label>Pilih Kategori</label>
        <select name="kategori_id[]" id="kategori_id" class="form-control" multiple>
            @foreach($kategoriList as $kategori)
                <option value="{{ $kategori->kategori_id }}">{{ $kategori->kategori_nama }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn bg-wallpaper-gradient text-white fw-bold btn-sm border-0" style="border-radius: 0.35rem;">
        Simpan
    </button>
    <button type="button" class="btn btn-dark btn-sm" id="btn-cancel-identifikasi">
        Batal
    </button>
</form>

{{-- Select2 CDN --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-selection {
    border-radius: 0.45rem !important;
    border: 1px solid #ced4da !important;
    padding: 4px 6px !important;
    min-height: 38px !important;
}

.select2-selection__choice {
    background: #6f42c1 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 0.45rem !important;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 3px 8px !important;
}

.select2-selection__choice__remove {
    color: #fff !important;
    margin-right: 6px;
    font-weight: bold;
}

.select2-results__option--highlighted {
    background: #9567e0 !important;
}
</style>

<script>
$(function() {

    $('#kategori_id').select2({
        placeholder: "Pilih kategori identifikasi...",
        width: '100%',
        allowClear: true
    });

    $(document).on('click', '#btn-cancel-identifikasi', function() {
        $('#form-identifikasi-container').slideUp().empty();
    });

    $(document).on('submit', '#form-identifikasi-awal', function(e){
        e.preventDefault();

        $.post("{{ route('interaksiAwal.store') }}", $(this).serialize(), function(res){
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });

                $('#form-identifikasi-container').slideUp().empty();

                let interaksiId = $("input[name='interaksi_id']").val();
                $('#identifikasi-tabel-container').html('<div class="text-center text-muted p-2">Memuat data...</div>');

                $.ajax({
                    url: "{{ route('interaksiAwal.tabel', '') }}/" + interaksiId,
                    cache: false,
                    success: function(html) {
                        $('#identifikasi-tabel-container').html(html);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire('Error!', 'Gagal memuat tabel identifikasi.', 'error');
                    }
                });
            }
        }).fail(function(){
            Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan', 'error');
        });
    });

});
</script>
