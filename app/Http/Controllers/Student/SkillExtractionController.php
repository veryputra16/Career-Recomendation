<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CVUpload;
use App\Services\AI\SkillExtractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class SkillExtractionController extends Controller
{
    /**
     * Trigger AI skill extraction for an uploaded CV document.
     */
    public function store(
        CVUpload $cvUpload,
        Request $request,
        SkillExtractionService $skillExtractionService
    ): RedirectResponse {
        $user = $request->user();
        $student = $user->student;

        // Ensure user is an authenticated student owning this CV
        if (! $student || $cvUpload->student_id !== $student->id) {
            abort(403, 'Unauthorized: You do not have permission to access this CV document.');
        }

        // Validate that CV has been parsed with text content
        if ($cvUpload->status !== 'processed' || empty(trim($cvUpload->extracted_text ?? ''))) {
            return redirect()->back()->with(
                'error',
                'Skill extraction cannot proceed: This CV has no extracted text content. Please upload a readable PDF document.'
            );
        }

        try {
            $result = $skillExtractionService->extractSkills($student, $cvUpload);

            $message = sprintf(
                'Skill extraction completed successfully! Identified %d catalog skills.',
                $result['matched_count']
            );

            if ($result['unmatched_count'] > 0) {
                $message .= sprintf(' (%d skill candidates not found in master catalog).', $result['unmatched_count']);
            }

            return redirect()->back()
                ->with('success', $message)
                ->with('extraction_result', $result);
        } catch (Throwable $e) {
            return redirect()->back()->with(
                'error',
                'Skill extraction failed: ' . $e->getMessage()
            );
        }
    }
}
