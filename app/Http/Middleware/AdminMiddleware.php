<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('admin.login');
        }

        $role = $request->user()->role;
        $roleValue = $role instanceof UserRole ? $role->value : $role;

        if (! in_array($roleValue, [UserRole::Admin->value, UserRole::SuperAdmin->value], true)) {
            return response()->view('admin.errors.403', [], 403);
        }

        return $next($request);
    }
}
