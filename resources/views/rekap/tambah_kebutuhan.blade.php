{{-- ========== KEBUTUHAN HARIAN ========== --}}
<div class="bg-warning text-dark px-3 py-2 mb-2 rounded">
    <strong>Data Produk</strong>
</div>

<input type="hidden" id="interaksi_id" name="interaksi_id" value="{{ $interaksi->interaksi_id ?? '' }}">
<div id="kebutuhan-container">
    <div class="row mb-2 kebutuhan-row">
        <div class="col-md-6">
            <select name="produk_id[]" class="form-control" required>
                <option value="">-- Pilih Produk --</option>
                @forelse($produks as $produk)
                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                @empty
                    <option value="">Tidak ada produk tersedia</option>
                @endforelse
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-kebutuhan">Hapus</button>
        </div>
    </div>
</div>

<button type="button" id="add-kebutuhan" class="btn btn-primary">+ Tambah Kebutuhan</button>
