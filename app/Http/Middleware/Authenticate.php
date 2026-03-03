<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Check for student guard
        if ($request->is('student/*') || $request->routeIs('student.*')) {
            return route('student.login');
        }
        
        // Check for teacher guard
        if ($request->is('teacher/*') || $request->routeIs('teacher.*')) {
            return route('teacher.login');
        }
        
        // Web routes redirect to login
        if (!$request->expectsJson() && !$request->is('api/*')) {
            return route('login');
        }

        // API routes: no redirect
        return null;
    }

    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!$request->user()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Web route: redirect
            return redirect($this->redirectTo($request));
        }

        return $next($request);
    }
}
