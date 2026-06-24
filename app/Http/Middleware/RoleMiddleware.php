<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * $roles bisa berisi satu role atau beberapa role sekaligus
     * Contoh penggunaan di route: middleware('role:super_admin,resepsionis')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek apakah user punya salah satu dari role yang diizinkan
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request); // Boleh masuk
            }
        }

        // Tidak punya role yang sesuai → tolak akses
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}