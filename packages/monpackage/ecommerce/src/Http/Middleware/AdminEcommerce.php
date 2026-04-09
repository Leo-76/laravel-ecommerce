<?php

namespace MonPackage\Ecommerce\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminEcommerce
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $estAdmin = false;

        // Compatible Spatie Permission
        if (method_exists($user, 'hasRole')) {
            $estAdmin = $user->hasRole('ecommerce-admin') || $user->hasRole('super-admin');
        } elseif (isset($user->ecommerce_admin)) {
            $estAdmin = (bool) $user->ecommerce_admin;
        } elseif (method_exists($user, 'isAdmin')) {
            $estAdmin = $user->isAdmin();
        }

        if (! $estAdmin) {
            abort(403, 'Accès réservé aux administrateurs e-commerce.');
        }

        return $next($request);
    }
}
