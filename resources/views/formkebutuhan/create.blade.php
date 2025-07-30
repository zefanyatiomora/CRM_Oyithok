@extends('layouts.template')
@section('content')
<div class="container-fluid">
    <div class="card card-primary mt-3">
        <div class="card-header">
            <h3 class="card-title">Formulir Kebutuhan Pelanggan</h3>
        </div>

        <form action="{{ route('kebutuhan.store') }}" method="POST">
            @csrf
            <div class="card-body row">
                {{-- Kolom kiri --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_nama">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="customer_nama" name="customer_nama" value="{{ old('customer_nama') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_nohp">No HP</label>
                        <input type="text" class="form-control" id="customer_nohp" name="customer_nohp" value="{{ old('customer_nohp') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_alamat">Alamat</label>
                        <textarea class="form-control" name="customer_alamat" id="customer_alamat" required>{{ old('customer_alamat') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="informasi_media">Informasi Media</label>
                        <input type="text" class="form-control" name="informasi_media" id="informasi_media" value="{{ old('informasi_media') }}" required>
                    </div>
                </div>

                {{-- Kolom kanan --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_chat">Tanggal Chat</label>
                        <input type="date" class="form-control" name="tanggal_chat" id="tanggal_chat" value="{{ old('tanggal_chat') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="produk_id">Pilih Produk</label>
                        <select name="produk_id[]" id="produk_id" class="form-control select2" multiple required>
                          @foreach ($produks as $produk)
        <option value="{{ $produk->id }}">{{ $produk->produk_nama }}</option>
    @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="identifikasi_kebutuhan">Identifikasi Kebutuhan</label>
                        <textarea class="form-control" name="identifikasi_kebutuhan" id="identifikasi_kebutuhan" required>{{ old('identifikasi_kebutuhan') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="media">Media</label>
                        <input type="text" class="form-control" name="media" id="media" value="{{ old('media') }}" required>
                    </div>
                </div>
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('css')
<!-- Select2 CSS (opsional jika mau tampil lebih rapi untuk multiple select) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function () {
        $('#produk_id').select2({
            placeholder: 'Pilih produk yang dibutuhkan',
            allowClear: true
        });
    });
</script>
@endpush
