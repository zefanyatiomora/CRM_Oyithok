{{-- resources/views/keterangan_invoice/edit.blade.php --}}
<div class="modal-header bg-wallpaper-gradient">
    <h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Edit Keterangan Invoice</h5>
    <button type="button" class="close text-white" data-dismiss="modal">
        <span>&times;</span>
    </button>
</div>

<form action="{{ route('keterangan_invoice.update', $keterangan->keterangan_id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="modal-body">
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="10" required>{{ old('keterangan', $keterangan->keterangan) }}</textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Batal
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>
    </div>
</form>
