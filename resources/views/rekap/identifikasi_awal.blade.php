{{-- ========== IDENTIFIKASI AWAL ========== --}}
<div class="bg-warning text-dark px-3 py-2 mb-2 rounded">
    <strong>Identifikasi Awal</strong>
</div>

<form id="form-identifikasi-awal">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">

    <div id="identifikasi-container">
        @foreach($interaksiAwalList as $index => $awal)
        <div class="row mb-2 identifikasi-row">
            <div class="col-md-8">
                <input type="text" name="kategori_nama[]" class="form-control form-control-sm" value="{{ $awal->kategori_nama }}" placeholder="Nama Kategori" required>
                <input type="hidden" name="awal_id[]" value="{{ $awal->awal_id }}">
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-danger btn-sm w-100 btn-remove-row">Hapus</button>
            </div>
        </div>
        @endforeach

        @if($interaksiAwalList->isEmpty())
        <div class="row mb-2 identifikasi-row">
            <div class="col-md-8">
                <input type="text" name="kategori_nama[]" class="form-control form-control-sm" placeholder="Nama Kategori" required>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-success btn-sm w-100 btn-add-row">Tambah</button>
            </div>
        </div>
        @endif
    </div>
</form>

<script>
$(document).ready(function(){

    // Tambah baris baru
    $(document).on('click', '.btn-add-row', function(){
        let newRow = `<div class="row mb-2 identifikasi-row">
            <div class="col-md-8">
                <input type="text" name="kategori_nama[]" class="form-control form-control-sm" placeholder="Nama Kategori" required>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-danger btn-sm w-100 btn-remove-row">Hapus</button>
            </div>
        </div>`;
        $('#identifikasi-container').append(newRow);
    });

    // Hapus baris
    $(document).on('click', '.btn-remove-row', function(){
        $(this).closest('.identifikasi-row').remove();
    });

    // Simpan data identifikasi awal
    $(document).on('submit', '#form-identifikasi-awal', function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('rekap.storeIdentifikasiAwal') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res){
                if(res.status === 'success'){
                    Swal.fire({
                        icon:'success',
                        title:'Berhasil!',
                        text:'Data identifikasi awal berhasil disimpan',
                        timer:1500,
                        showConfirmButton:false
                    });
                    // Bisa reload modal / list jika perlu
                } else {
                    Swal.fire({icon:'error', title:'Gagal!', text: res.message || 'Gagal menyimpan data'});
                }
            },
            error: function(err){
                console.error(err);
                Swal.fire({icon:'error', title:'Error!', text:'Terjadi kesalahan saat menyimpan'});
            }
        });
    });

});
</script>
