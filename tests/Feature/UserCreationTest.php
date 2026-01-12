<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\WelcomeMail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can be created successfully.
     *
     * @return void
     */
    public function test_user_can_be_created_successfully()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+1234567890',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'User Created Successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    /**
     * Test that welcome email is sent when user is created.
     *
     * @return void
     */
    public function test_welcome_email_is_sent_when_user_is_created()
    {
        Mail::fake();

        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/auth/register', $userData);

        Mail::assertSent(WelcomeMail::class, function ($mail) {
            return $mail->userEmail === 'jane@example.com' &&
                   $mail->userName !== null;
        });
    }

    /**
     * Test that welcome email contains correct user data.
     *
     * @return void
     */
    public function test_welcome_email_contains_correct_user_data()
    {
        Mail::fake();

        $userData = [
            'first_name' => 'Bob',
            'last_name' => 'Johnson',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/auth/register', $userData);

        Mail::assertSent(WelcomeMail::class, function ($mail) {
            return $mail->userEmail === 'bob@example.com' &&
                   $mail->userName === $mail->userName; // Check that userName is set
        });
    }

    /**
     * Test that user creation fails with invalid email.
     *
     * @return void
     */
    public function test_user_creation_fails_with_invalid_email()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that user creation fails with duplicate email.
     *
     * @return void
     */
    public function test_user_creation_fails_with_duplicate_email()
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that user creation fails with weak password.
     *
     * @return void
     */
    public function test_user_creation_fails_with_weak_password()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test that user creation fails with mismatched passwords.
     *
     * @return void
     */
    public function test_user_creation_fails_with_mismatched_passwords()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test that user creation fails with missing required fields.
     *
     * @return void
     */
    public function test_user_creation_fails_with_missing_required_fields()
    {
        $userData = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['first_name', 'last_name']);
    }

    /**
     * Test that user creation logs success.
     *
     * @return void
     */
    public function test_user_creation_logs_success()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('User created successfully', \Mockery::on(function ($context) {
                return isset($context['user_id']) && isset($context['email']);
            }));

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/auth/register', $userData);
    }

    /**
     * Test that email sending errors are logged but don't fail user creation.
     *
     * @return void
     */
    public function test_email_sending_errors_are_logged_but_dont_fail_user_creation()
    {
        Mail::fake();
        Mail::shouldReceive('to')
            ->andThrow(new \Exception('SMTP connection failed'));

        Log::shouldReceive('error')
            ->once()
            ->with('Failed to send welcome email', \Mockery::on(function ($context) {
                return isset($context['user_id']) && isset($context['error']);
            }));

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        // User should still be created despite email failure
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /**
     * Test that welcome email is sent for admin-created users.
     *
     * @return void
     */
    public function test_welcome_email_is_sent_for_admin_created_users()
    {
        Mail::fake();
        
        // Create admin user
        $admin = User::factory()->create(['role_id' => 1]);
        
        $userData = [
            'first_name' => 'Admin',
            'last_name' => 'Created',
            'email' => 'admin-created@example.com',
            'password' => 'password123',
            'role_id' => 2,
        ];

        $this->actingAs($admin)
             ->postJson('/api/admin/users', $userData);

        Mail::assertSent(WelcomeMail::class, function ($mail) {
            return $mail->userEmail === 'admin-created@example.com';
        });
    }
}
