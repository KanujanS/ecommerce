@extends('layout')
@section('title', 'Login')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="container d-flex justify-content-center mt-5">

    <div class="card p-4 shadow" style="width: 420px; border-radius: 10px;">

        <h3 class="text-center mb-4">Login</h3>
        <div class="mt-5">
            @if ($errors->any())
            <div class="col-12">
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{$error}}</div>
                @endforeach
            </div>
            @endif
            @if (session()->has('error'))
            <div class="alert alert-danger">{{session('error')}}</div>
            @endif
            @if (session()->has('success'))
            <div class="alert alert-success">{{session('success')}}</div>
            @endif
        </div>
        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" class="form-control" name="email" required placeholder="Enter your email">
            </div>

            {{-- Password --}}
            <div class="mb-3 position-relative">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="fa fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            {{-- Forgot Password --}}
            <div class="mb-3 text-end">
                <a href="/forgot-password" class="text-success">Forgot password?</a>
            </div>

            {{-- Login Button --}}
            <button type="submit" class="btn btn-outline-success w-100 mb-3">Login</button>

            {{-- Register --}}
            <div class="text-center">
                <span>Don't have an account?</span>
                <a href="/registration" class="text-success">Register</a>
            </div>

        </form>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const toggleButton = document.getElementById("togglePassword");
                const passwordField = document.getElementById("password");
                const eyeIcon = document.getElementById("eyeIcon");

                toggleButton.addEventListener("click", function() {
                    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
                    passwordField.setAttribute("type", type);

                    // Toggle the eye / eye-slash icon
                    if (type === "password") {
                        eyeIcon.classList.remove("fa-eye-slash");
                        eyeIcon.classList.add("fa-eye");
                    } else {
                        eyeIcon.classList.remove("fa-eye");
                        eyeIcon.classList.add("fa-eye-slash");
                    }
                });
            });
        </script>

    </div>

</div>
@endsection