<?php

use App\Http\Controllers\AuthManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})-> name('home');
Route::get('/login', [AuthManager::class, 'login'])-> name('login');
Route::post('/login', [AuthManager::class, 'loginPost'])-> name('login.post');
Route::get('/registration', [AuthManager::class, 'registration'])-> name('registration');
Route::post('/registration', [AuthManager::class, 'registrationPost'])-> name('registration.post');
Route::get('/logout', [AuthManager::class, 'logout'])-> name('logout');

Route::middleware('guest')->group(function () {
    // Forgot/Reset Password (backend wired to AuthManager)
    Route::get('/forgot-password', [AuthManager::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthManager::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthManager::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthManager::class, 'reset'])->name('password.update');
});