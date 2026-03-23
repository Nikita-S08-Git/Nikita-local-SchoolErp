<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class StudentViewComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Get the authenticated student
        $student = Auth::guard('student')->user();
        
        // Share student with all views
        $view->with('student', $student);
    }
}
