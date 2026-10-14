<?php

namespace Tests\Feature;

use App\Models\CVUpload;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CVUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function getSamplePdfContent(string $text = 'John Doe - Senior Software Engineer'): string
    {
        $streamContent = "BT\n/F1 12 Tf\n50 700 Td\n({$text}) Tj\nET\n";
        $streamLen = strlen($streamContent);

        $obj1 = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $obj2 = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $obj3 = "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n";
        $obj4 = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $obj5 = "5 0 obj\n<< /Length {$streamLen} >>\nstream\n{$streamContent}endstream\nendobj\n";

        $header = "%PDF-1.4\n";
        $off1 = strlen($header);
        $off2 = $off1 + strlen($obj1);
        $off3 = $off2 + strlen($obj2);
        $off4 = $off3 + strlen($obj3);
        $off5 = $off4 + strlen($obj4);
        $xrefOffset = $off5 + strlen($obj5);

        $xref = "xref\n0 6\n"
            . "0000000000 65535 f \n"
            . sprintf("%010d 00000 n \n", $off1)
            . sprintf("%010d 00000 n \n", $off2)
            . sprintf("%010d 00000 n \n", $off3)
            . sprintf("%010d 00000 n \n", $off4)
            . sprintf("%010d 00000 n \n", $off5);

        $trailer = "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF\n";

        return $header . $obj1 . $obj2 . $obj3 . $obj4 . $obj5 . $xref . $trailer;
    }

    public function test_unauthenticated_user_cannot_access_cv_management(): void
    {
        $response = $this->get('/student/cv');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_upload_cv(): void
    {
        $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

        $response = $this->post('/student/cv', [
            'cv' => $file,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_access_student_cv_management(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/student/cv');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_upload_cv(): void
    {
        $admin = User::factory()->admin()->create();
        $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

        $response = $this->actingAs($admin)->post('/student/cv', [
            'cv' => $file,
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_student_can_view_cv_management_page(): void
    {
        $studentUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->get('/student/cv');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Student/CV/Index')
            ->has('currentCv')
            ->has('history')
        );
    }

    public function test_student_can_upload_valid_pdf_and_text_is_extracted(): void
    {
        Storage::fake('local');

        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $pdfContent = $this->getSamplePdfContent('Jane Smith Laravel Developer');
        $file = UploadedFile::fake()->createWithContent('resume.pdf', $pdfContent);

        $response = $this->actingAs($studentUser)->post('/student/cv', [
            'cv' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cv_uploads', [
            'student_id' => $student->id,
            'original_filename' => 'resume.pdf',
            'status' => 'processed',
        ]);

        $cvUpload = CVUpload::where('student_id', $student->id)->latest('id')->first();
        $this->assertNotNull($cvUpload);
        $this->assertStringContainsString('Jane Smith Laravel Developer', $cvUpload->extracted_text);

        Storage::disk('local')->assertExists($cvUpload->file_path);
    }

    public function test_upload_rejects_non_pdf_files(): void
    {
        $studentUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentUser->id]);

        $file = UploadedFile::fake()->create('document.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($studentUser)->post('/student/cv', [
            'cv' => $file,
        ]);

        $response->assertSessionHasErrors('cv');
    }

    public function test_upload_rejects_oversized_files(): void
    {
        $studentUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentUser->id]);

        // 6000 KB > 5120 KB (5MB) limit
        $file = UploadedFile::fake()->create('huge.pdf', 6000, 'application/pdf');

        $response = $this->actingAs($studentUser)->post('/student/cv', [
            'cv' => $file,
        ]);

        $response->assertSessionHasErrors('cv');
    }

    public function test_unreadable_pdf_handled_gracefully(): void
    {
        Storage::fake('local');

        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        // A PDF structure with empty stream
        $emptyPdf = "%PDF-1.4\n1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n2 0 obj << /Type /Pages /Kids [] /Count 0 >> endobj\nxref\n0 3\n0000000000 65535 f \ntrailer << /Size 3 /Root 1 0 R >>\nstartxref\n100\n%%EOF\n";
        $file = UploadedFile::fake()->createWithContent('scanned.pdf', $emptyPdf);

        $response = $this->actingAs($studentUser)->post('/student/cv', [
            'cv' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('cv_uploads', [
            'student_id' => $student->id,
            'original_filename' => 'scanned.pdf',
            'status' => 'failed',
            'extracted_text' => null,
        ]);
    }

    public function test_student_can_download_their_own_cv(): void
    {
        Storage::fake('local');

        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $storedPath = 'cv/test_file.pdf';
        Storage::disk('local')->put($storedPath, 'Fake PDF Content');

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'my_resume.pdf',
            'stored_filename' => 'test_file.pdf',
            'file_path' => $storedPath,
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
        ]);

        $response = $this->actingAs($studentUser)->get("/student/cv/{$cvUpload->id}/download");

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=my_resume.pdf');
    }

    public function test_student_cannot_download_another_students_cv(): void
    {
        Storage::fake('local');

        $studentAUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentAUser->id]);

        $studentBUser = User::factory()->student()->create();
        $studentB = Student::factory()->create(['user_id' => $studentBUser->id]);

        $storedPath = 'cv/student_b.pdf';
        Storage::disk('local')->put($storedPath, 'Secret PDF');

        $cvUploadB = CVUpload::create([
            'student_id' => $studentB->id,
            'original_filename' => 'student_b_resume.pdf',
            'stored_filename' => 'student_b.pdf',
            'file_path' => $storedPath,
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
        ]);

        $response = $this->actingAs($studentAUser)->get("/student/cv/{$cvUploadB->id}/download");

        $response->assertStatus(403);
    }
}
