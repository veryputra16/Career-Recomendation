<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_student_profile(): void
    {
        $response = $this->get('/student/profile');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_update_student_profile(): void
    {
        $response = $this->put('/student/profile', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_access_student_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/student/profile');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_update_student_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put('/student/profile', [
            'full_name' => 'Admin Hacked',
            'email' => 'admin@example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_student_can_view_profile(): void
    {
        $studentUser = User::factory()->student()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);
        $student = Student::factory()->create([
            'user_id' => $studentUser->id,
            'full_name' => 'Budi Santoso',
            'student_number' => '12345678',
            'university' => 'Universitas Indonesia',
            'faculty' => 'Fakultas Ilmu Komputer',
            'major' => 'Informatika',
            'semester' => 5,
            'graduation_year' => 2026,
            'bio' => 'Interested in backend development.',
        ]);

        $response = $this->actingAs($studentUser)->get('/student/profile');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Student/Profile')
            ->has('student')
            ->where('student.full_name', 'Budi Santoso')
            ->where('student.student_number', '12345678')
            ->where('student.university', 'Universitas Indonesia')
            ->where('student.major', 'Informatika')
            ->where('student.semester', 5)
            ->where('student.graduation_year', 2026)
            ->has('user')
            ->where('user.email', 'budi@example.com')
        );
    }

    public function test_authenticated_student_can_update_profile(): void
    {
        $studentUser = User::factory()->student()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $student = Student::factory()->create([
            'user_id' => $studentUser->id,
            'full_name' => 'Old Name',
        ]);

        $response = $this->actingAs($studentUser)->put('/student/profile', [
            'full_name' => 'Budi Santoso, S.Kom',
            'email' => 'budi.new@example.com',
            'student_number' => '210101999',
            'university' => 'Universitas Indonesia',
            'faculty' => 'Fakultas Ilmu Komputer',
            'major' => 'Sistem Informasi',
            'semester' => 6,
            'graduation_year' => 2026,
            'bio' => 'Aspiring AI engineer.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Profile updated successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $studentUser->id,
            'name' => 'Budi Santoso, S.Kom',
            'email' => 'budi.new@example.com',
            'role' => 'student',
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'user_id' => $studentUser->id,
            'full_name' => 'Budi Santoso, S.Kom',
            'student_number' => '210101999',
            'university' => 'Universitas Indonesia',
            'faculty' => 'Fakultas Ilmu Komputer',
            'major' => 'Sistem Informasi',
            'semester' => 6,
            'graduation_year' => 2026,
            'bio' => 'Aspiring AI engineer.',
        ]);
    }

    public function test_updating_profile_requires_full_name_and_email(): void
    {
        $studentUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->put('/student/profile', [
            'full_name' => '',
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['full_name', 'email']);
    }

    public function test_updating_profile_requires_valid_email(): void
    {
        $studentUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->put('/student/profile', [
            'full_name' => 'Valid Name',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_student_cannot_take_another_users_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $studentUser = User::factory()->student()->create(['email' => 'mine@example.com']);
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->put('/student/profile', [
            'full_name' => 'Valid Name',
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_student_can_keep_their_existing_email(): void
    {
        $studentUser = User::factory()->student()->create(['email' => 'keepme@example.com']);
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->put('/student/profile', [
            'full_name' => 'Valid Name',
            'email' => 'keepme@example.com',
            'university' => 'Institut Teknologi Bandung',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_semester_and_graduation_year_validation_bounds(): void
    {
        $studentUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->put('/student/profile', [
            'full_name' => 'Valid Name',
            'email' => 'valid@example.com',
            'semester' => 20, // max is 14
            'graduation_year' => 1995, // min is 2000
        ]);

        $response->assertSessionHasErrors(['semester', 'graduation_year']);
    }

    public function test_protected_fields_cannot_be_manipulated_through_profile_form(): void
    {
        $studentUser = User::factory()->student()->create([
            'role' => 'student',
        ]);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $otherUser = User::factory()->create();

        $response = $this->actingAs($studentUser)->put('/student/profile', [
            'full_name' => 'Valid Name',
            'email' => 'valid@example.com',
            'role' => 'admin',
            'id' => 9999,
            'user_id' => $otherUser->id,
        ]);

        $studentUser->refresh();
        $student->refresh();

        $this->assertEquals('student', $studentUser->role);
        $this->assertEquals($studentUser->id, $student->user_id);
    }
}
