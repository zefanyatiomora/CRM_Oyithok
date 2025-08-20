@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Identifikasi Kebutuhan</h4>

    <form action="{{ route('tambahkebutuhan.update', $interaksi->interaksi_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="identifikasi_kebutuhan" class="form-label">Identifikasi Kebutuhan</label>
            <textarea name="identifikasi_kebutuhan" id="identifikasi_kebutuhan" rows="4" class="form-control" required>{{ old('identifikasi_kebutuhan', $interaksi->identifikasi_kebutuhan) }}</textarea>
            @error('identifikasi_kebutuhan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="tanggal_chat" class="form-label">Tanggal Chat</label>
            <input type="date" name="tanggal_chat" id="tanggal_chat" class="form-control" 
                   value="{{ old('tanggal_chat', $interaksi->tanggal_chat ? $interaksi->tanggal_chat->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
            @error('tanggal_chat')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('tambahkebutuhan.index', $interaksi->customer_id) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
