<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered_for_guests(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
        );
    }

    public function test_admin_can_authenticate_and_is_redirected_to_admin_route(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin');
    }

    public function test_student_can_authenticate_and_is_redirected_to_student_route(): void
    {
        $studentUser = User::factory()->student()->create([
            'email' => 'student@example.com',
            'password' => 'password123',
        ]);
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->post('/login', [
            'email' => 'student@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($studentUser);
        $response->assertRedirect('/student');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'correct-password',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_unauthenticated_users_are_redirected_to_login_from_protected_admin_route(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_users_are_redirected_to_login_from_protected_student_route(): void
    {
        $response = $this->get('/student');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_route(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Dashboard')
        );
    }

    public function test_student_can_access_student_route(): void
    {
        $studentUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->get('/student');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Student/Dashboard')
        );
    }

    public function test_student_cannot_access_admin_route(): void
    {
        $studentUser = User::factory()->student()->create();

        $response = $this->actingAs($studentUser)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_student_route(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this->actingAs($adminUser)->get('/student');

        $response->assertStatus(403);
    }

    public function test_authenticated_admin_is_redirected_when_accessing_login_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect('/admin');
    }

    public function test_authenticated_student_is_redirected_when_accessing_login_page(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/login');

        $response->assertRedirect('/student');
    }

    public function test_users_can_logout_and_session_is_invalidated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
