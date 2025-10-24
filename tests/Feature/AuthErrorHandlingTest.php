<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Exceptions\AuthException;

class AuthErrorHandlingTest extends TestCase
{
    /**
     * Test that AuthException provides specific error messages
     *
     * @return void
     */
    public function test_auth_exception_provides_specific_error_messages()
    {
        // Test user not found exception
        $exception = AuthException::userNotFound('test@example.com');
        $this->assertEquals('USER_NOT_FOUND', $exception->getErrorCode());
        $this->assertEquals('No account found with email: test@example.com', $exception->getMessage());
        $this->assertEquals(['email' => 'test@example.com'], $exception->getErrorDetails());

        // Test invalid credentials exception
        $exception = AuthException::invalidCredentials();
        $this->assertEquals('INVALID_CREDENTIALS', $exception->getErrorCode());
        $this->assertEquals('The provided credentials are incorrect.', $exception->getMessage());

        // Test email already exists exception
        $exception = AuthException::emailAlreadyExists('existing@example.com');
        $this->assertEquals('EMAIL_ALREADY_EXISTS', $exception->getErrorCode());
        $this->assertEquals('A user already exists with email address: existing@example.com', $exception->getMessage());
        $this->assertEquals(['email' => 'existing@example.com'], $exception->getErrorDetails());

        // Test user not verified exception
        $exception = AuthException::userNotVerified('unverified@example.com');
        $this->assertEquals('USER_NOT_VERIFIED', $exception->getErrorCode());
        $this->assertEquals('Your email unverified@example.com has not been verified yet. Please check your email for verification instructions.', $exception->getMessage());
        $this->assertEquals(['email' => 'unverified@example.com'], $exception->getErrorDetails());

        // Test account suspended exception
        $exception = AuthException::accountSuspended('suspended@example.com');
        $this->assertEquals('ACCOUNT_SUSPENDED', $exception->getErrorCode());
        $this->assertEquals('Your account with email suspended@example.com has been suspended. Please contact support.', $exception->getMessage());
        $this->assertEquals(['email' => 'suspended@example.com'], $exception->getErrorDetails());
    }

    /**
     * Test login error handling with specific scenarios
     *
     * @return void
     */
    public function test_login_error_handling()
    {
        // Test login with non-existent email
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'code' => 'USER_NOT_FOUND',
            'message' => 'No account found with email: nonexistent@example.com'
        ]);

        // Create a user but don't verify email
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
            'status' => 'approved'
        ]);

        // Test login with unverified email
        $response = $this->postJson('/api/auth/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'code' => 'USER_NOT_VERIFIED',
            'message' => 'Your email unverified@example.com has not been verified yet. Please check your email for verification instructions.'
        ]);

        // Create a suspended user
        $suspendedUser = User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'suspended'
        ]);

        // Test login with suspended account
        $response = $this->postJson('/api/auth/login', [
            'email' => 'suspended@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'code' => 'ACCOUNT_SUSPENDED',
            'message' => 'Your account with email suspended@example.com has been suspended. Please contact support.'
        ]);

        // Test login with incorrect password
        $activeUser = User::factory()->create([
            'email' => 'active@example.com',
            'password' => Hash::make('correctpassword'),
            'email_verified_at' => now(),
            'status' => 'approved'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'active@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'code' => 'INVALID_CREDENTIALS',
            'message' => 'The provided credentials are incorrect.'
        ]);
    }

    /**
     * Test registration error handling
     *
     * @return void
     */
    public function test_registration_error_handling()
    {
        // Test registration with duplicate email
        User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(409);
        $response->assertJsonFragment([
            'code' => 'EMAIL_ALREADY_EXISTS',
            'message' => 'A user already exists with email address: existing@example.com'
        ]);

        // Test registration with invalid email format
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'code' => 'VALIDATION_ERROR'
        ]);

        // Test registration with weak password
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'new@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'code' => 'VALIDATION_ERROR'
        ]);
    }

    /**
     * Test password reset error handling
     *
     * @return void
     */
    public function test_password_reset_error_handling()
    {
        // Test forgot password with non-existent email
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'code' => 'USER_NOT_FOUND',
            'message' => 'No account found with email: nonexistent@example.com'
        ]);

        // Test reset password with invalid token
        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'token' => 'invalidtoken',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'code' => 'INVALID_TOKEN',
            'message' => 'The provided token is invalid.'
        ]);

        // Test reset password with expired token
        // This would require setting up a database record with an old timestamp
        // For now, we'll just test the validation
        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'token' => '123456',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(422); // Should fail validation since no token exists
    }
}