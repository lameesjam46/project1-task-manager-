<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Mail\PasswordResetCodeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    // Web-based password reset methods
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        $token = Str::random(64);
        $expiresAt = Carbon::now()->addMinutes(60);

        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'token' => $token,
                'expires_at' => $expiresAt,
            ]
        );

        // Send password reset email with token
        Mail::to($request->email)->send(new PasswordResetCodeMail($token, true));

        return back()->with('status', 'We have emailed your password reset link!');
    }

    public function showResetForm(Request $request, $token)
    {
        $reset = PasswordReset::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            return redirect()->route('password.request')->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $reset->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = PasswordReset::where('email', $request->email)
            ->where('token', $request->token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset record
        $reset->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }

    // Existing API methods
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = Auth::user();
        if (!$user || $user->email !== $request->email) {
            return response()->json(['message' => 'Invalid email for the logged-in user.'], 403);
        }

        $code = random_int(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(10);

        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => $expiresAt,
            ]
        );

        Mail::to($request->email)->send(new PasswordResetCodeMail($code));

        return response()->json(['message' => 'Verification code sent.']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $reset = PasswordReset::where('email', $user->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid or expired code.'], 403);
        }

        return response()->json(['message' => 'Code verified.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = PasswordReset::where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid or expired code.'], 403);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Optionally delete the reset record
        $reset->delete();

        return response()->json(['message' => 'Password reset successful.']);
    }
}
