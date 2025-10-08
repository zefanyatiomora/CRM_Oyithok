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

            // Tutup form identifikasi
            $('#form-identifikasi-container').slideUp().html('');

            // Ambil interaksi_id dari hidden input
            let interaksiId = $("input[name='interaksi_id']").val();

            // Reload modal rekap
            $("#myModal").load("{{ url('rekap') }}/" + interaksiId + "/show_ajax", function() {
                $("#crudModal").modal('hide'); 
                $("#myModal").modal('show'); 
            });

        }
    }).fail(function(){
        Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan', 'error');
    });
});
