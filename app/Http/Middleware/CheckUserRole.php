<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        // Check if student guard is active
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            
            // Check if route is allowed for students
            $studentAllowedRoutes = [
                'student.dashboard',
                'student.timetable',
                'student.attendance',
                'student.profile',
                'student.logout',
            ];

            $currentRoute = $request->route()->getName();
            
            if (!in_array($currentRoute, $studentAllowedRoutes)) {
                abort(403, 'You are not authorized to access this route.');
            }
            
            return $next($request);
        } elseif (Auth::check()) {
            $user = Auth::user();
            $userRole = $user->roles->first()->name ?? 'student';
            
            // Check if user has any of the allowed roles
            if (empty($allowedRoles) || in_array($userRole, $allowedRoles)) {
                return $next($request);
            }
            
            abort(403, 'You are not authorized to access this route.');
        }
        
        // If not authenticated, redirect to login
        return redirect()->route('login');
    }
}
