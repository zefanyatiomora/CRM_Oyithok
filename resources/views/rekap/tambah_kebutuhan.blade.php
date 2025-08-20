@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Tambah Kebutuhan untuk {{ $customer->customer_nama }}</h4>

    <form action="{{ route('tambahkebutuhan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="customer_id" value="{{ $customer->customer_id }}">

        <div class="mb-3">
            <label for="produk_id" class="form-label">Pilih Produk</label>
            <select name="produk_id" id="produk_id" class="form-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($produks as $produk)
                    <option value="{{ $produk->produk_id }}">
                        {{ $produk->produk_kode }} - {{ $produk->produk_nama }}
                    </option>
                @endforeach
            </select>
            @error('produk_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('tambahkebutuhan.index', $customer->customer_id) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
