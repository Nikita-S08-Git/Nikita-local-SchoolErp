<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User\Student;
use App\Policies\StudentDocumentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Add your policies here
        Student::class => StudentDocumentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Fee management permissions
        Gate::define('manage_fee_structures', function ($user) {
            return $user->hasAnyRole(['admin', 'accounts_staff', 'accountant']);
        });

        // Scholarship verification permissions
        Gate::define('verify_scholarships', function ($user) {
            return $user->hasAnyRole(['student_section', 'accountant']);
        });

        // View fee reports
        Gate::define('view_fee_reports', function ($user) {
            return $user->hasAnyRole(['admin', 'accounts_staff', 'principal', 'accountant']);
        });
    }
}