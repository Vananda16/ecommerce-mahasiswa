<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->is_blocked) {
            $reason = $user->blocked_reason ?: 'pelanggaran aturan marketplace';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => "Akun Anda diblokir. Alasan: {$reason}."])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route($user->homeRoute()));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'nim' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
            // Hanya penjual & pembeli yang boleh mendaftar sendiri. Admin dibuat lewat seeder.
            'role' => ['required', Rule::in([User::SELLER, User::BUYER])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User($data);
        $user->role = $data['role'];
        $user->save();

        if ($user->isSeller()) {
            $user->store()->create(['name' => 'Toko '.$user->name]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->homeRoute())
            ->with('success', 'Registrasi berhasil. Selamat datang di '.config('app.name').'!');
    }

    public function dashboard(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->homeRoute());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
