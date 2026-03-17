<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // For teachers/admins
            if ($user->hasRole(['teacher', 'admin', 'principal'])) {
                // Check if password change is required
                if (!empty($user->password_changed_at)) {
                    return $next($request);
                }
                
                // Allow access to change password page and logout
                if ($request->is('password/change') || $request->is('logout')) {
                    return $next($request);
                }
                
                return redirect()->route('password.change');
            }
        } elseif (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $user = $student->user;
            
            // Check if password change is required
            if (!empty($user->password_changed_at)) {
                return $next($request);
            }
            
            // Allow access to change password page and logout
            if ($request->is('student/profile/change-password') || $request->is('student/logout')) {
                return $next($request);
            }
            
            return redirect()->route('student.profile.change-password');
        }
        
        return $next($request);
    }
}
