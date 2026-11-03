<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check() || session('role') === 'admin') {
            return $next($request);
        }

        if (Auth::guard('web')->check() && session('role') === 'customer') {
            return redirect()->route('dashboard_user')->with('login_error', 'unauthorized_admin');
        }

        return redirect()->route('login')->with([
            'login_error' => 'auth_required',
            'active_tab' => 'admin'
        ]);
    }
}

