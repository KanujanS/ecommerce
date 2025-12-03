@extends('layout')
@section('title', 'Registration')

@section('content')
<div class="container d-flex justify-content-center mt-5">

    <div class="card p-4 shadow" style="width: 420px; border-radius: 10px;">

        <h3 class="text-center mb-4">Create Account</h3>
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
        <form action="{{ route('registration.post') }}" method="POST">
            @csrf

            {{-- Full Name --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" class="form-control" name="name" required placeholder="Enter your full name">
            </div>

            {{-- Mobile Number --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Phone Number</label>
                <input type="tel" class="form-control" name="phone" required placeholder="Enter your phone number">
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" class="form-control" name="email" required placeholder="Enter your email">
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" name="password" required placeholder="Create a password">
            </div>

            {{-- Create Account Button --}}
            <button type="submit" class="btn btn-outline-success w-100 mb-3">Create Account</button>

            {{-- Login Link --}}
            <div class="text-center">
                <span>Already have an account?</span>
                <a href="/login" class="text-success">Login</a>
            </div>

        </form>

    </div>

</div>
@endsection