@extends('layouts.app')

@section('title', 'Tambah Interaksi')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Data Interaksi</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('interaksi.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="tanggal_chat" class="form-label">Tanggal Chat</label>
            <input type="date" class="form-control" name="tanggal_chat" id="tanggal_chat" value="{{ old('tanggal_chat') }}" required>
        </div>

        <div class="mb-3">
            <label for="produk_id" class="form-label">Nama Produk</label>
            <select name="produk_id" id="produk_id" class="form-control" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($produks as $produk)
                    <option value="{{ $produk->id }}" {{ old('produk_id') == $produk->id ? 'selected' : '' }}>
                        {{ $produk->produk_nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="identifikasi_kebutuhan" class="form-label">Identifikasi Kebutuhan</label>
            <textarea name="identifikasi_kebutuhan" id="identifikasi_kebutuhan" class="form-control" rows="4" required>{{ old('identifikasi_kebutuhan') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('interaksi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
