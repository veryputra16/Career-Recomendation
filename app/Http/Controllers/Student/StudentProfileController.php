<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateStudentProfileRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentProfileController extends Controller
{
    /**
     * Show the student profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        // Ensure student record exists
        $student = $user->student;
        if (! $student) {
            $student = $user->student()->create([
                'full_name' => $user->name,
            ]);
        }

        return Inertia::render('Student/Profile', [
            'student' => [
                'id' => $student->id,
                'student_number' => $student->student_number,
                'full_name' => $student->full_name,
                'university' => $student->university,
                'faculty' => $student->faculty,
                'major' => $student->major,
                'semester' => $student->semester,
                'graduation_year' => $student->graduation_year,
                'bio' => $student->bio,
            ],
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'created_at' => $user->created_at?->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Update the student profile in storage.
     */
    public function update(UpdateStudentProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update user account details (name synchronized with student full_name)
        $user->update([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
        ]);

        // Update or create associated student record
        $student = $user->student;
        if (! $student) {
            $student = new Student();
            $student->user_id = $user->id;
        }

        $student->fill([
            'student_number' => $validated['student_number'] ?? null,
            'full_name' => $validated['full_name'],
            'university' => $validated['university'] ?? null,
            'faculty' => $validated['faculty'] ?? null,
            'major' => $validated['major'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'graduation_year' => $validated['graduation_year'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);
        $student->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
