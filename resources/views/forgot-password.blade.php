@extends('layout')
@section('title', 'Forgot Password')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="card p-4 shadow" style="width: 420px; border-radius: 10px;">
        <h3 class="text-center mb-4">Forgot Password?</h3>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-outline-primary w-100 mb-3">Send Reset Link</button>
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-primary">Back to Login</a>
            </div>
        </form>
    </div>
</div>
@endsection