<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Support both single role and multiple roles (pipe-separated or comma-separated)
        $allowedRoles = [];
        foreach ($roles as $roleParam) {
            // Handle pipe-separated roles (e.g., "teacher|class_teacher")
            if (strpos($roleParam, '|') !== false) {
                $allowedRoles = array_merge($allowedRoles, explode('|', $roleParam));
            } elseif (strpos($roleParam, ',') !== false) {
                $allowedRoles = array_merge($allowedRoles, explode(',', $roleParam));
            } else {
                $allowedRoles[] = $roleParam;
            }
        }

        // Check if user has any of the allowed roles
        foreach ($allowedRoles as $role) {
            $role = trim($role);
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Log for debugging
        \Log::warning('Role check failed', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->getRoleNames()->toArray(),
            'required_roles' => $allowedRoles,
        ]);

        abort(403, 'Unauthorized access. Required roles: ' . implode(', ', $allowedRoles));
    }
}
