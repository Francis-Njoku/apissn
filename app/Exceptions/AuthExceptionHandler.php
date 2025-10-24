<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use App\Helpers\ApiResponseHelper;

class AuthExceptionHandler
{
    /**
     * Handle authentication exceptions.
     *
     * @param \Throwable $exception
     * @return JsonResponse
     */
    public static function handle(\Throwable $exception): JsonResponse
    {
        if ($exception instanceof AuthException) {
            return self::handleAuthException($exception);
        }

        if ($exception instanceof AuthenticationException) {
            return self::handleAuthenticationException($exception);
        }

        if ($exception instanceof ValidationException) {
            return self::handleValidationException($exception);
        }

        // Handle generic authentication-related errors
        return ApiResponseHelper::unauthorized(
            'Authentication failed. Please check your credentials and try again.',
            'AUTH_ERROR'
        );
    }

    /**
     * Handle custom AuthException.
     *
     * @param AuthException $exception
     * @return JsonResponse
     */
    private static function handleAuthException(AuthException $exception): JsonResponse
    {
        return ApiResponseHelper::error(
            $exception->getMessage(),
            $exception->getErrorCode(),
            $exception->getErrorDetails(),
            $exception->getCode()
        );
    }

    /**
     * Handle Laravel's AuthenticationException.
     *
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    private static function handleAuthenticationException(AuthenticationException $exception): JsonResponse
    {
        $guard = $exception->guards()[0] ?? null;

        switch ($guard) {
            case 'api':
                return ApiResponseHelper::unauthorized(
                    'API token is invalid or expired. Please log in again.',
                    'INVALID_API_TOKEN'
                );

            case 'web':
                return ApiResponseHelper::unauthorized(
                    'Session expired. Please log in again.',
                    'SESSION_EXPIRED'
                );

            default:
                return ApiResponseHelper::unauthorized(
                    'Authentication required. Please log in to continue.',
                    'AUTH_REQUIRED'
                );
        }
    }

    /**
     * Handle validation exceptions in authentication context.
     *
     * @param ValidationException $exception
     * @return JsonResponse
     */
    private static function handleValidationException(ValidationException $exception): JsonResponse
    {
        $errors     = $exception->errors();
        $firstError = !empty($errors) ? reset($errors)[0] : 'Validation failed';

        // Check for specific authentication-related validation errors
        if (isset($errors['email'])) {
            if (in_array('The email has already been taken.', $errors['email'])) {
                return ApiResponseHelper::error(
                    'A user already exists with that email address.',
                    'EMAIL_ALREADY_EXISTS',
                    ['email' => $errors['email']],
                    409
                );
            }

            if (in_array('The email must be a valid email address.', $errors['email'])) {
                return ApiResponseHelper::error(
                    'The email address provided is not valid.',
                    'INVALID_EMAIL',
                    ['email' => $errors['email']],
                    422
                );
            }
        }

        if (isset($errors['password'])) {
            if (in_array('The password must be at least 8 characters.', $errors['password'])) {
                return ApiResponseHelper::error(
                    'The password must be at least 8 characters long.',
                    'WEAK_PASSWORD',
                    ['password' => $errors['password']],
                    422
                );
            }

            if (in_array('The password confirmation does not match.', $errors['password'])) {
                return ApiResponseHelper::error(
                    'The password confirmation does not match.',
                    'PASSWORD_MISMATCH',
                    ['password' => $errors['password']],
                    422
                );
            }
        }

        // Generic validation error
        return ApiResponseHelper::validationError(
            $errors,
            'Validation failed. Please check your input and try again.',
            422
        );
    }
}