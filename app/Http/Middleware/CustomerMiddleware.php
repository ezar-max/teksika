<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check() || session('role') === 'admin') {
            return redirect()->route('dashboard_admin');
        }

        if (Auth::guard('web')->check() || Auth::check()) {
            return $next($request);
        }

        return redirect()->route('login')->with([
            'login_error' => 'auth_required',
            'active_tab' => 'customer'
        ]);
    }
}

