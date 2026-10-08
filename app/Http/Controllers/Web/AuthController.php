<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
       

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Restrict access to admin-only (Manager role)
            if (Auth::user() && Auth::user()->role === 'Admin') {
                return redirect()->intended(route('dashboard'));
            }
            // Not authorized: logout and show message
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages([
                'email' => [__('app.not_authorized')],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__('app.invalid_credentials')],
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

   

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
} 