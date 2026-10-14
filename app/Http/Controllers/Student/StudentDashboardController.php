<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController extends Controller
{
    /**
     * Display the Student Dashboard with student career overview state.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $student = $user->student;

        $stats = [
            'has_cv' => false,
            'cv_count' => 0,
            'skills_count' => 0,
            'recommendations_count' => 0,
            'job_matches_count' => 0,
            'latest_cv' => null,
        ];

        if ($student) {
            $latestCv = $student->cvUploads()->latest()->first();

            $stats = [
                'has_cv' => $latestCv !== null,
                'cv_count' => $student->cvUploads()->count(),
                'skills_count' => $student->skills()->count(),
                'recommendations_count' => $student->careerRecommendations()->count(),
                'job_matches_count' => $student->jobMatches()->count(),
                'latest_cv' => $latestCv ? [
                    'id' => $latestCv->id,
                    'file_name' => $latestCv->original_filename ?? $latestCv->file_name,
                    'uploaded_at' => $latestCv->created_at?->format('Y-m-d H:i'),
                ] : null,
                'recent_skills' => $student->skills()
                    ->withPivot('confidence_score', 'source')
                    ->orderByPivot('confidence_score', 'desc')
                    ->take(8)
                    ->get()
                    ->map(fn ($s) => [
                        'id' => $s->id,
                        'name' => $s->name,
                        'category' => $s->category,
                        'confidence_score' => $s->pivot->confidence_score,
                    ]),
            ];
        }

        return Inertia::render('Student/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
