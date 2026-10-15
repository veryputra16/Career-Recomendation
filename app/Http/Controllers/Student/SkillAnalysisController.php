<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Skill\SkillAnalysisService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SkillAnalysisController extends Controller
{
    public function __construct(
        protected SkillAnalysisService $skillAnalysisService
    ) {}

    /**
     * Display the student's skill analysis page.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $student = $user->student;

        $analysis = $this->skillAnalysisService->analyze($student);

        $latestCv = $student?->cvUploads()->latest()->first();

        return Inertia::render('Student/Skills/Index', [
            'analysis' => $analysis,
            'hasCv' => $latestCv !== null,
            'latestCv' => $latestCv ? [
                'id' => $latestCv->id,
                'filename' => $latestCv->original_filename ?? $latestCv->file_name,
                'has_extracted_text' => ! empty(trim((string) ($latestCv->extracted_text ?? ''))),
            ] : null,
        ]);
    }
}
