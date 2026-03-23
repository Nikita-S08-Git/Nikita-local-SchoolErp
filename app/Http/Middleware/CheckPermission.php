<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Check if user has the specific permission
        if (!$user->hasPermissionTo($permission)) {
            // Check if user has any of the permissions (for wildcard matching)
            $permissions = explode('|', $permission);
            $hasAnyPermission = false;
            
            foreach ($permissions as $perm) {
                if ($user->hasPermissionTo(trim($perm))) {
                    $hasAnyPermission = true;
                    break;
                }
            }
            
            if (!$hasAnyPermission) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access Denied. You do not have permission to perform this action.'
                    ], 403);
                }
                
                return redirect()->route('dashboard')
                    ->with('error', 'Access Denied. You do not have permission to access this module.');
            }
        }

        return $next($request);
    }
}
