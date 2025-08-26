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
        if(res.status === 'success'){
            Swal.fire('Berhasil!', res.message, 'success');

            // Hapus form
            $('#form-identifikasi-container').slideUp().html('');

            // Reload container tetap
            $.get("{{ route('interaksiAwal.list', $interaksi_id) }}", function(html){
                $('#identifikasi-container').html(html);
            });
        }
    }).fail(function(){
        Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan', 'error');
    });
});

</script>
