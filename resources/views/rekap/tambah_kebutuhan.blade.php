<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                {{ isset($interaksi) ? 'Edit Kebutuhan' : 'Tambah Kebutuhan' }}
                - {{ $customer->customer_nama ?? $interaksi->customer->customer_nama ?? '-' }}
            </h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-tambah-kebutuhan" 
                  action="{{ isset($interaksi) ? route('tambahkebutuhan.update',$interaksi->interaksi_id) : route('tambahkebutuhan.store') }}" 
                  method="POST">
                @csrf
                @if(isset($interaksi)) @method('POST') @endif
                <input type="hidden" name="customer_id" value="{{ $customer->customer_id ?? $interaksi->customer_id }}">

                <div class="mb-3">
                    <label for="produk_id" class="form-label">Pilih Produk</label>
                    <select name="produk_id" id="produk_id" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produks ?? [] as $produk)
                            <option value="{{ $produk->produk_id }}"
                                {{ isset($interaksi) && $interaksi->produk_id == $produk->produk_id ? 'selected' : '' }}>
                                {{ $produk->produk_kode }} - {{ $produk->produk_nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('produk_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </form>
        </div>
    </div>
</div>
