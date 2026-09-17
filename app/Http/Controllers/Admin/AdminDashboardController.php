<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Job;
use App\Models\Skill;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard with baseline system counts.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'counts' => [
                'students' => Student::count(),
                'skills' => Skill::count(),
                'careers' => Career::count(),
                'jobs' => Job::count(),
            ],
        ]);
    }
}
