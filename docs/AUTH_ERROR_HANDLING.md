# Authentication Error Handling System

## Overview

This document describes the enhanced authentication error handling system implemented in the application. The system provides specific, user-friendly error messages for various authentication scenarios, improving the user experience and making debugging easier.

## Error Types

### AuthException Class

The `App\Exceptions\AuthException` class provides specific error types for different authentication scenarios:

#### Login Errors

-   `INVALID_CREDENTIALS`: Incorrect email or password
-   `USER_NOT_FOUND`: No account exists with the provided email
-   `USER_NOT_VERIFIED`: Email has not been verified
-   `ACCOUNT_SUSPENDED`: Account has been suspended by admin
-   `ACCOUNT_LOCKED`: Account locked due to too many failed attempts
-   `TOO_MANY_ATTEMPTS`: Too many failed login attempts

#### Registration Errors

-   `EMAIL_ALREADY_EXISTS`: A user already exists with that email address
-   `INVALID_EMAIL`: The email address format is invalid
-   `WEAK_PASSWORD`: Password does not meet security requirements
-   `MISSING_REQUIRED_FIELDS`: Required fields are missing from registration

#### Password Reset Errors

-   `INVALID_TOKEN`: Password reset token is invalid
-   `TOKEN_EXPIRED`: Password reset token has expired
-   `USER_NOT_FOUND`: No account found for password reset

#### General Authentication Errors

-   `SESSION_EXPIRED`: User session has expired
-   `INVALID_REFRESH_TOKEN`: Refresh token is invalid
-   `PASSWORD_MISMATCH`: Password confirmation does not match
-   `CURRENT_PASSWORD_INCORRECT`: Current password is incorrect for updates

## Error Response Format

All authentication errors follow a standardized response format:

```json
{
    "success": false,
    "code": "ERROR_CODE",
    "message": "User-friendly error message",
    "errors": {
        "field_name": ["Specific error details"]
    },
    "data": null
}
```

## Implementation Details

### Controllers Updated

1. **API Authentication Controller** (`app/Http/Controllers/Api/Auth/UserController.php`)

    - Enhanced login method with specific error messages
    - Improved registration validation and error handling
    - Detailed password reset error messages

2. **Web Login Controller** (`app/Http/Controllers/Auth/LoginController.php`)

    - Custom validation messages
    - Specific error handling for different user states
    - Enhanced logging for security

3. **Web Registration Controller** (`app/Http/Controllers/Auth/RegisterController.php`)
    - Detailed validation error messages
    - Email existence checking
    - Improved user creation process

### Exception Handling

-   **AuthExceptionHandler**: Centralized handling of authentication exceptions
-   **Handler**: Updated to integrate with Laravel's exception handling system

## Error Code Reference

| Error Code                   | HTTP Status | Description                    |
| ---------------------------- | ----------- | ------------------------------ |
| `INVALID_CREDENTIALS`        | 401         | Incorrect email or password    |
| `USER_NOT_FOUND`             | 404         | No account found with email    |
| `USER_NOT_VERIFIED`          | 403         | Email not verified             |
| `ACCOUNT_SUSPENDED`          | 403         | Account suspended              |
| `EMAIL_ALREADY_EXISTS`       | 409         | Email already registered       |
| `INVALID_EMAIL`              | 422         | Invalid email format           |
| `WEAK_PASSWORD`              | 422         | Password too weak              |
| `INVALID_TOKEN`              | 401         | Invalid reset token            |
| `TOKEN_EXPIRED`              | 401         | Reset token expired            |
| `MISSING_REQUIRED_FIELDS`    | 422         | Missing required fields        |
| `PASSWORD_MISMATCH`          | 422         | Password confirmation mismatch |
| `CURRENT_PASSWORD_INCORRECT` | 401         | Current password incorrect     |
| `TOO_MANY_ATTEMPTS`          | 429         | Too many login attempts        |
| `ACCOUNT_LOCKED`             | 423         | Account locked                 |
| `SESSION_EXPIRED`            | 401         | Session expired                |
| `INVALID_REFRESH_TOKEN`      | 401         | Invalid refresh token          |

## Testing

Comprehensive tests have been added in `tests/Feature/AuthErrorHandlingTest.php` to verify:

-   Specific error messages for different scenarios
-   Proper HTTP status codes
-   Error response format consistency
-   Edge cases and validation

## Usage Examples

### Login Error Response

```json
// When user doesn't exist
{
    "success": false,
    "code": "USER_NOT_FOUND",
    "message": "No account found with email: nonexistent@example.com",
    "errors": {
        "email": "nonexistent@example.com"
    },
    "data": null
}

// When password is incorrect
{
    "success": false,
    "code": "INVALID_CREDENTIALS",
    "message": "The provided credentials are incorrect.",
    "errors": null,
    "data": null
}

// When email is not verified
{
    "success": false,
    "code": "USER_NOT_VERIFIED",
    "message": "Your email user@example.com has not been verified yet. Please check your email for verification instructions.",
    "errors": {
        "email": "user@example.com"
    },
    "data": null
}
```

### Registration Error Response

```json
// When email already exists
{
    "success": false,
    "code": "EMAIL_ALREADY_EXISTS",
    "message": "A user already exists with email address: existing@example.com",
    "errors": {
        "email": "existing@example.com"
    },
    "data": null
}

// When password is too weak
{
    "success": false,
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "errors": {
        "password": ["The password must be at least 8 characters."]
    },
    "data": null
}
```

### Password Reset Error Response

```json
// When token is invalid
{
    "success": false,
    "code": "INVALID_TOKEN",
    "message": "The provided token is invalid.",
    "errors": null,
    "data": null
}

// When token is expired
{
    "success": false,
    "code": "TOKEN_EXPIRED",
    "message": "The provided token has expired.",
    "errors": null,
    "data": null
}
```

## Security Considerations

-   Failed login attempts are logged for security monitoring
-   Sensitive information is not exposed in error messages
-   Rate limiting is implemented for authentication endpoints
-   Password reset tokens have expiration times
-   User account status is checked before authentication

## Future Enhancements

-   Implement account lockout after multiple failed attempts
-   Add email verification status checking
-   Implement two-factor authentication error handling
-   Add password strength requirements
-   Implement CAPTCHA for failed login attempts
