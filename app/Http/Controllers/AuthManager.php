<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthManager extends Controller
{
    function login() {
        return view('login');
    }
    function registration() {
        return view('registration');
    }
    function loginPost(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('home'))->with("success", "You have successfully logged in");
        }
        return redirect(route('login'))->with("error", "Login details are not valid");
    }
}
