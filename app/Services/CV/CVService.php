<?php

namespace App\Services\CV;

use App\Models\CVUpload;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class CVService
{
    public function __construct(
        protected CVParserService $parserService
    ) {}

    /**
     * Store the uploaded CV file securely and process its text content.
     */
    public function uploadAndProcess(Student $student, UploadedFile $file): CVUpload
    {
        $storedFilename = sprintf('%d_%s.pdf', $student->id, (string) Str::uuid());

        // Store privately on the local disk
        $filePath = $file->storeAs('cv', $storedFilename, 'local');

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'file_path' => $filePath,
            'file_size' => $file->getSize() ?: 0,
            'mime_type' => $file->getMimeType() ?: 'application/pdf',
            'status' => 'uploaded',
            'uploaded_at' => now(),
        ]);

        $absolutePath = Storage::disk('local')->path($filePath);

        try {
            $extractedText = $this->parserService->extractText($absolutePath);

            if (trim($extractedText) === '') {
                $cvUpload->update([
                    'status' => 'failed',
                    'extracted_text' => null,
                    'processed_at' => now(),
                ]);
            } else {
                $cvUpload->update([
                    'status' => 'processed',
                    'extracted_text' => $extractedText,
                    'processed_at' => now(),
                ]);
            }
        } catch (Throwable $e) {
            $cvUpload->update([
                'status' => 'failed',
                'extracted_text' => null,
                'processed_at' => now(),
            ]);
        }

        return $cvUpload->fresh();
    }

    /**
     * Generate a secure download response for a student's CV file.
     */
    public function download(CVUpload $cvUpload): StreamedResponse
    {
        if (! Storage::disk('local')->exists($cvUpload->file_path)) {
            abort(404, 'The requested document could not be found.');
        }

        return Storage::disk('local')->download(
            $cvUpload->file_path,
            $cvUpload->original_filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Delete a CV upload and its physical storage file.
     */
    public function delete(CVUpload $cvUpload): bool
    {
        if (Storage::disk('local')->exists($cvUpload->file_path)) {
            Storage::disk('local')->delete($cvUpload->file_path);
        }

        return $cvUpload->delete();
    }
}
