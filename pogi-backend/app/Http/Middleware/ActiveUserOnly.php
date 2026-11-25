<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserOnly
{
    /**
     * Memastikan hanya user berstatus aktif yang dapat berinteraksi dengan sistem
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $accStatus = strtoupper((string) ($user->account_status ?? ($user->is_active ? 'ACTIVE' : 'INACTIVE')));
        if ($user && ($accStatus !== 'ACTIVE' || !$user->is_active)) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $msg = $accStatus === 'BLOCK'
                ? 'Akun Anda telah diblokir (BLOCK) oleh Super Admin. Silakan hubungi Sekretariat PP POGI.'
                : 'Akun pengurus Anda berstatus non-aktif (INACTIVE). Silakan hubungi administrator.';

            return redirect()->route('login')->with('error', $msg);
        }

        return $next($request);
    }
}
