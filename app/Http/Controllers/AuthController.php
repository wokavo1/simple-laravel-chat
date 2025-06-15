<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse {
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required|string",
        ]);

        if (Auth::attempt($credentials)) {
            // recommended actions after user login
            $request->session()->regenerate();

            return redirect()->route('index');
        }

        throw ValidationException::withMessages([
            "credentials" => "The provided credentials do not match our records."
        ]);
    }

    public function loginPage(Request $request) {
        return view("auth.login");
    }

    public function register(Request $request) {
        $validated = $request->validate([
            "email" => "required|email|unique:users",
            "password" => "required|string|min:8|confirmed",
            "password_confirmation" => "required",
        ]);

        $validated["name"] = $validated["email"];

        $user = User::create($validated);

        Auth::login($user);

        return redirect()->route('index');
    }

    public function registerPage(Request $request) {
        return view("auth.register");
    }

    public function logout(Request $request) {
        Auth::logout();

        // recommended actions after user logout
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('index');
    }
}
