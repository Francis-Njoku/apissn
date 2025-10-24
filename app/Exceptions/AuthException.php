<?php

namespace App\Exceptions;

use Exception;

class AuthException extends Exception
{
    public const INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
    public const USER_NOT_FOUND = 'USER_NOT_FOUND';
    public const USER_NOT_VERIFIED = 'USER_NOT_VERIFIED';
    public const ACCOUNT_SUSPENDED = 'ACCOUNT_SUSPENDED';
    public const EMAIL_ALREADY_EXISTS = 'EMAIL_ALREADY_EXISTS';
    public const WEAK_PASSWORD = 'WEAK_PASSWORD';
    public const INVALID_EMAIL = 'INVALID_EMAIL';
    public const MISSING_REQUIRED_FIELDS = 'MISSING_REQUIRED_FIELDS';
    public const INVALID_TOKEN = 'INVALID_TOKEN';
    public const TOKEN_EXPIRED = 'TOKEN_EXPIRED';
    public const PASSWORD_MISMATCH = 'PASSWORD_MISMATCH';
    public const CURRENT_PASSWORD_INCORRECT = 'CURRENT_PASSWORD_INCORRECT';
    public const ACCOUNT_LOCKED = 'ACCOUNT_LOCKED';
    public const TOO_MANY_ATTEMPTS = 'TOO_MANY_ATTEMPTS';
    public const EMAIL_NOT_VERIFIED = 'EMAIL_NOT_VERIFIED';
    public const INVALID_REFRESH_TOKEN = 'INVALID_REFRESH_TOKEN';
    public const SESSION_EXPIRED = 'SESSION_EXPIRED';

    protected $code;
    protected $errorDetails;

    public function __construct(
        string $message = 'Authentication error',
        string $code = 'AUTH_ERROR',
        ?array $errorDetails = null,
        int $statusCode = 400
    ) {
        parent::__construct($message, $statusCode);
        $this->code         = $code;
        $this->errorDetails = $errorDetails;
    }

    public function getErrorCode(): string
    {
        return $this->code;
    }

    public function getErrorDetails(): ?array
    {
        return $this->errorDetails;
    }

    public static function invalidCredentials(?string $details = null): self
    {
        return new self(
            'The provided credentials are incorrect.',
            self::INVALID_CREDENTIALS,
            $details ? ['details' => $details] : null,
            401
        );
    }

    public static function userNotFound(string $email): self
    {
        return new self(
            "No account found with email: {$email}",
            self::USER_NOT_FOUND,
            ['email' => $email],
            404
        );
    }

    public static function userNotVerified(string $email): self
    {
        return new self(
            "Your email {$email} has not been verified yet. Please check your email for verification instructions.",
            self::USER_NOT_VERIFIED,
            ['email' => $email],
            403
        );
    }

    public static function accountSuspended(string $email): self
    {
        return new self(
            "Your account with email {$email} has been suspended. Please contact support.",
            self::ACCOUNT_SUSPENDED,
            ['email' => $email],
            403
        );
    }

    public static function emailAlreadyExists(string $email): self
    {
        return new self(
            "A user already exists with email address: {$email}",
            self::EMAIL_ALREADY_EXISTS,
            ['email' => $email],
            409
        );
    }

    public static function weakPassword(): self
    {
        return new self(
            'The password is too weak. Please use a stronger password with at least 8 characters including uppercase, lowercase, numbers, and special characters.',
            self::WEAK_PASSWORD,
            null,
            422
        );
    }

    public static function invalidEmail(string $email): self
    {
        return new self(
            "The email address {$email} is not valid.",
            self::INVALID_EMAIL,
            ['email' => $email],
            422
        );
    }

    public static function missingRequiredFields(array $fields): self
    {
        return new self(
            'The following required fields are missing: ' . implode(', ', $fields),
            self::MISSING_REQUIRED_FIELDS,
            ['missing_fields' => $fields],
            422
        );
    }

    public static function invalidToken(?string $token = null): self
    {
        return new self(
            'The provided token is invalid.',
            self::INVALID_TOKEN,
            $token ? ['token' => $token] : null,
            401
        );
    }

    public static function tokenExpired(?string $token = null): self
    {
        return new self(
            'The provided token has expired.',
            self::TOKEN_EXPIRED,
            $token ? ['token' => $token] : null,
            401
        );
    }

    public static function passwordMismatch(): self
    {
        return new self(
            'The password confirmation does not match.',
            self::PASSWORD_MISMATCH,
            null,
            422
        );
    }

    public static function currentPasswordIncorrect(): self
    {
        return new self(
            'The current password provided is incorrect.',
            self::CURRENT_PASSWORD_INCORRECT,
            null,
            401
        );
    }

    public static function accountLocked(string $email): self
    {
        return new self(
            "Your account with email {$email} has been locked due to too many failed login attempts. Please try again later or contact support.",
            self::ACCOUNT_LOCKED,
            ['email' => $email],
            423
        );
    }

    public static function tooManyAttempts(string $email): self
    {
        return new self(
            "Too many failed login attempts for email {$email}. Please try again later.",
            self::TOO_MANY_ATTEMPTS,
            ['email' => $email],
            429
        );
    }

    public static function emailNotVerified(string $email): self
    {
        return new self(
            "Your email {$email} has not been verified. Please check your email for verification instructions.",
            self::EMAIL_NOT_VERIFIED,
            ['email' => $email],
            403
        );
    }

    public static function invalidRefreshToken(): self
    {
        return new self(
            'The refresh token is invalid or has been revoked.',
            self::INVALID_REFRESH_TOKEN,
            null,
            401
        );
    }

    public static function sessionExpired(): self
    {
        return new self(
            'Your session has expired. Please log in again.',
            self::SESSION_EXPIRED,
            null,
            401
        );
    }
}