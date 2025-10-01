@extends('layouts.template')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-body text-center">
            <!-- Foto User -->
            <img src="{{ $user->image ?? asset('default-avatar.png') }}" 
                 alt="User Avatar" 
                 class="rounded-circle mb-3" width="120" height="120">

            <!-- Nama dan Level -->
            <h3 class="fw-bold mb-1">{{ $user->nama }}</h3>
            <p class="text-muted">{{ $user->level->level_nama ?? 'Tidak ada level' }}</p>

            <!-- Info Tambahan -->
            <ul class="list-group text-start mt-3">
                <li class="list-group-item"><strong>Username:</strong> {{ $user->username }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $user->email ?? '-' }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
