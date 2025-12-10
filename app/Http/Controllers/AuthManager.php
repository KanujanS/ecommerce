<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthManager extends Controller
{
    function login()
    {
        if (Auth::check()) {
            return redirect(route('home'));
        }
        return view('login');
    }
    function registration()
    {
        if (Auth::check()) {
            return redirect(route('home'));
        }
        return view('registration');
    }

    function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            if (!Auth::user()->is_verified) {
                Auth::logout();
                return redirect()->route('verify.otp.form', ['email' => $request->email])
                    ->with("error", "Please verify OTP before logging in.");
            }

            return redirect()->intended(route('home'))->with("success", "Logged in successfully");
        }

        return redirect(route('login'))->with("error", "Invalid login details");
    }

    function registrationPost(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|digits:10|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ---- Generate OTP ----
        $otp = $this->generateOtp();
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // ---- Send OTP (email / SMS) ----
        $this->sendOtpEmail($user->email, $otp);

        // Redirect to OTP page
        return redirect()->route('verify.otp.form', ['email' => $user->email])
            ->with("success", "OTP sent to your email.");
    }

    private function generateOtp()
    {
        return rand(100000, 999999); // 6-digit OTP
    }

    private function sendOtpEmail($email, $otp)
    {
        Mail::raw("Your OTP is: $otp", function ($message) use ($email) {
            $message->to($email)->subject('Your OTP Code');
        });
    }

    public function showVerifyOtpForm(Request $request)
    {
        return view('verify-otp', ['email' => $request->email]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_code != $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP']);
        }

        if ($user->otp_expires_at < now()) {
            return back()->withErrors(['otp' => 'OTP expired.']);
        }

        $user->is_verified = true;
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return redirect()->route('login')->with("success", "Your account is verified. Please login.");
    }

    public function sendForgotOtp(Request $request)
{
    $request->validate(['email' => 'required|email|exists:users,email']);

    $user = User::where('email', $request->email)->first();

    $otp = $this->generateOtp();
    $user->otp_code = $otp;
    $user->otp_expires_at = now()->addMinutes(10);
    $user->save();

    $this->sendOtpEmail($user->email, $otp);

    return back()->with('success', 'OTP sent to your email.');
}

public function verifyForgotOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:6',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || $user->otp_code != $request->otp) {
        return back()->withErrors(['otp' => 'Invalid OTP']);
    }

    if ($user->otp_expires_at < now()) {
        return back()->withErrors(['otp' => 'OTP expired']);
    }

    // OTP verified → redirect to reset password form
    return redirect()->route('password.reset', ['email' => $request->email, 'token' => Str::random(60)]);
}
    // ========== FORGOT PASSWORD METHODS ==========

    /**
     * Show Forgot Password Form
     */
    public function showForgotPasswordForm()
    {
        return view('forgot-password');
    }

    /**
     * Send Password Reset Link Email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show Reset Password Form
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Process Password Reset (FIXED: Proper event dispatch)
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // ✅ FIXED: Use Laravel helper (no lint error)
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Generate password reset token
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email address.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to send password reset link. Please try again.',
        ], 400);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to reset password. Please try again.',
        ], 400);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Check if user is authenticated
     */
    public function checkAuth(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::check() ? Auth::user() : null,
        ]);
    }
    function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect(route('login'))->with("success", "You have logged out successfully.");
    }
}
