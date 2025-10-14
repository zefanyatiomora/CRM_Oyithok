<form id="form-identifikasi-awal">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi_id }}">
    <div class="form-group">
        <label>Pilih Kategori</label>
        <select name="kategori_id[]" class="form-control" multiple>
            @foreach($kategoriList as $kategori)
                <option value="{{ $kategori->kategori_id }}">{{ $kategori->kategori_nama }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
</form>

<script>
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

            // Tutup form & kosongkan
            $('#form-identifikasi-container').slideUp().empty();

            // Ambil interaksi_id
            let interaksiId = $("input[name='interaksi_id']").val();

            // Ganti isi tabel dengan "loading" sementara
            $('#identifikasi-tabel-container').html('<div class="text-center text-muted p-2">Memuat data...</div>');

            // Muat ulang tabel tanpa refresh
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
</script>
