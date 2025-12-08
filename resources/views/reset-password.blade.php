@extends('layout')
@section('title', 'Reset Password')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="card p-4 shadow" style="width: 420px; border-radius: 10px;">
        <h3 class="text-center mb-4">Reset Password</h3>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email', $email ?? '') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror" 
                           name="password" required placeholder="Enter new password">
                    <button type="button" class="btn btn-sm p-0 position-absolute end-0 top-50 translate-middle-y me-3" id="togglePassword">
                        <i class="bi bi-eye fs-6"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                       name="password_confirmation" required placeholder="Confirm new password">
            </div>

            <button type="submit" class="btn btn-outline-success w-100 mb-3">Reset Password</button>
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-primary">Back to Login</a>
            </div>
        </form>

        <script>
            // Eye toggle script (same as registration)
            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = togglePassword?.parentElement.querySelector('input[type="password"]:first-of-type');
                const icon = togglePassword?.querySelector('i');
                if (togglePassword && passwordInput && icon) {
                    togglePassword.addEventListener('click', function() {
                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            icon.classList.remove('bi-eye');
                            icon.classList.add('bi-eye-slash');
                        } else {
                            passwordInput.type = 'password';
                            icon.classList.remove('bi-eye-slash');
                            icon.classList.add('bi-eye');
                        }
                    });
                }
            });
        </script>
    </div>
</div>
@endsection
