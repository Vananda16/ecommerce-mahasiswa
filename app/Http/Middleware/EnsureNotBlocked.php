<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBlocked
{
    /**
     * Akun yang diblokir admin langsung dikeluarkan pada request berikutnya.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_blocked) {
            $reason = $user->blocked_reason ?: 'pelanggaran aturan marketplace';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => "Akun Anda diblokir. Alasan: {$reason}."]);
        }

        return $next($request);
    }
}
