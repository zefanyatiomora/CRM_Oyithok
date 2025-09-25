@extends('layouts.auth')

@section('title', 'Login - Customer Relationship Management Wallpaper ID')

@section('content')
<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center" 
     style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
    
    <!-- Branding / Header -->
    <div class="text-center mb-4">
        <!-- Logo Section -->
        <div class="d-flex justify-content-center align-items-center mb-4">
            <div class="bg-white rounded-circle shadow d-flex justify-content-center align-items-center me-3" style="width: 90px; height: 90px;">
                <img src="{{ asset('images/logo-wpm.png') }}" alt="Logo WPM" style="max-height: 60px;">
            </div>
            <div class="bg-white rounded-circle shadow d-flex justify-content-center align-items-center" style="width: 90px; height: 90px;">
                <img src="{{ asset('images/logo-wpi.png') }}" alt="Logo WPI" style="max-height: 60px;">
            </div>
        </div>

        <h2 class="fw-bold text-white">Customer Relationship Management</h2>
        <h5 class="text-light">Wallpaper ID</h5>
    </div>

    <!-- Login Card -->
    <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 500px; transition: transform 0.2s;">
        <div class="card-header text-center text-white rounded-top-4" 
             style="background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);">
            <h4 class="mb-0 fw-semibold">Login</h4>
        </div>
        <div class="card-body p-5">
            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control rounded-3" required autofocus>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control rounded-3" required>
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label for="remember" class="form-check-label">Ingat saya</label>
                </div>

                <!-- Submit Button -->
                <button class="btn w-100 rounded-3 fw-semibold text-white" 
                        style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-sign-in-alt me-1"></i> Login
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-4 text-light small">
        &copy; {{ date('Y') }} Wallpaper ID - CRM System
    </div>
</div>
@endsection
