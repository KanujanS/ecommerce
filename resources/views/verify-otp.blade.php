@extends('layout')
@section('title','Verify OTP')

@section('content')

<div class="container d-flex justify-content-center align-items-center py-5">
    <div class="card shadow-lg p-4" style="max-width: 430px; width: 100%; border-radius: 15px;">

        {{-- Title --}}
        <h2 class="text-center mb-2 fw-bold">Verify OTP</h2>
        <p class="text-center text-muted mb-4">
            A 6-digit OTP has been sent to your email address.
        </p>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- OTP Form --}}
        <form action="{{ route('verify.otp') }}" method="POST" id="otpForm">
            @csrf

            <div class="d-flex justify-content-center gap-2 mb-4">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text"
                        maxlength="1"
                        name="otp[]"
                        class="otp-input form-control text-center fw-bold"
                        style="width: 45px; height: 45px; font-size: 1.2rem;"
                        required>
                @endfor
            </div>

            <input type="hidden" name="email" value="{{ $email }}">

            {{-- Button --}}
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    Verify OTP
                </button>
            </div>
        </form>

        {{-- Resend Section --}}
        <div class="text-center mt-4">
            <span class="text-muted">Didn't receive the OTP?</span>

            <form action="{{ url('/resend-otp') }}" method="POST" class="d-inline-block ms-1">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit"
                    id="resendBtn"
                    class="btn btn-link p-0 fw-semibold"
                    disabled>
                    Resend OTP (<span id="timer">30</span>s)
                </button>
            </form>
        </div>

    </div>
</div>

{{-- OTP Script --}}
<script>
    // Auto move to next input
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, index) => {

        input.addEventListener('input', () => {
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === "Backspace" && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

    });

    // Timer
    let timeLeft = 30;
    const timerDisplay = document.getElementById('timer');
    const resendBtn = document.getElementById('resendBtn');

    const countdown = setInterval(() => {
        timeLeft--;
        timerDisplay.textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(countdown);
            resendBtn.disabled = false;
        }
    }, 1000);
</script>

@endsection