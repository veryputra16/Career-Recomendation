<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UploadCVRequest;
use App\Models\CVUpload;
use App\Services\CV\CVService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CVController extends Controller
{
    /**
     * Display the CV management interface with current CV status and history.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $student = $user->student;

        if (! $student) {
            $student = $user->student()->create([
                'full_name' => $user->name,
            ]);
        }

        $latestCv = $student->cvUploads()->latest('id')->first();

        $history = $student->cvUploads()
            ->latest('id')
            ->get()
            ->map(fn (CVUpload $cv) => [
                'id' => $cv->id,
                'original_filename' => $cv->original_filename,
                'file_size' => $cv->file_size,
                'status' => $cv->status,
                'has_extracted_text' => ! empty($cv->extracted_text),
                'extracted_text_length' => strlen($cv->extracted_text ?? ''),
                'uploaded_at' => $cv->uploaded_at?->format('Y-m-d H:i'),
                'processed_at' => $cv->processed_at?->format('Y-m-d H:i'),
            ]);

        return Inertia::render('Student/CV/Index', [
            'currentCv' => $latestCv ? [
                'id' => $latestCv->id,
                'original_filename' => $latestCv->original_filename,
                'file_size' => $latestCv->file_size,
                'status' => $latestCv->status,
                'extracted_text' => $latestCv->extracted_text,
                'uploaded_at' => $latestCv->uploaded_at?->format('Y-m-d H:i'),
                'processed_at' => $latestCv->processed_at?->format('Y-m-d H:i'),
            ] : null,
            'history' => $history,
        ]);
    }

    /**
     * Handle the upload and automated text extraction of a CV document.
     */
    public function store(UploadCVRequest $request, CVService $cvService): RedirectResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (! $student) {
            $student = $user->student()->create([
                'full_name' => $user->name,
            ]);
        }

        $cvUpload = $cvService->uploadAndProcess($student, $request->file('cv'));

        if ($cvUpload->status === 'processed') {
            return redirect()->back()->with('success', 'CV uploaded and text extracted successfully.');
        }

        return redirect()->back()->with(
            'error',
            'CV uploaded, but readable text could not be extracted. Please ensure your PDF is not a scanned image or password-protected.'
        );
    }

    /**
     * Securely download a student's own uploaded CV file.
     */
    public function download(CVUpload $cvUpload, Request $request, CVService $cvService): StreamedResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (! $student || $cvUpload->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this document.');
        }

        return $cvService->download($cvUpload);
    }
}
