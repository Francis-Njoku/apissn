<?php

namespace App\Helpers;

use App\Http\Responses\ErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ApiResponseHelper
{
    /**
     * Create a standardized error response.
     *
     * @param string $message
     * @param string|int $code
     * @param array|null $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function error(
        string $message,
        $code = 'ERROR',
        ?array $errors = null,
        int $statusCode = 400
    ): JsonResponse {
        return ErrorResponse::create($message, $code, $errors, $statusCode);
    }

    /**
     * Create a validation error response.
     *
     * @param array $errors
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function validationError(
        array $errors,
        string $message = 'Validation failed',
        int $statusCode = 422
    ): JsonResponse {
        return ErrorResponse::validationError($errors, $message, $statusCode);
    }

    /**
     * Create a not found error response.
     *
     * @param string $message
     * @param string $code
     * @return JsonResponse
     */
    public static function notFound(
        string $message = 'Resource not found',
        string $code = 'NOT_FOUND'
    ): JsonResponse {
        return ErrorResponse::notFound($message, $code);
    }

    /**
     * Create an unauthorized error response.
     *
     * @param string $message
     * @param string $code
     * @return JsonResponse
     */
    public static function unauthorized(
        string $message = 'Unauthorized',
        string $code = 'UNAUTHORIZED'
    ): JsonResponse {
        return ErrorResponse::unauthorized($message, $code);
    }

    /**
     * Create a server error response.
     *
     * @param string $message
     * @param string $code
     * @param array|null $errors
     * @return JsonResponse
     */
    public static function serverError(
        string $message = 'Internal server error',
        string $code = 'SERVER_ERROR',
        ?array $errors = null
    ): JsonResponse {
        return ErrorResponse::serverError($message, $code, $errors);
    }

    /**
     * Create a forbidden error response.
     *
     * @param string $message
     * @param string $code
     * @return JsonResponse
     */
    public static function forbidden(
        string $message = 'Forbidden',
        string $code = 'FORBIDDEN'
    ): JsonResponse {
        return ErrorResponse::forbidden($message, $code);
    }

    /**
     * Validate request data and return error response if validation fails.
     * Returns null if validation passes, error response if it fails.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return JsonResponse|null
     */
    public static function validateRequest(
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ): ?JsonResponse {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            return self::validationError($validator->errors()->toArray());
        }

        return null;
    }

    /**
     * Handle try-catch blocks and return standardized error response.
     *
     * @param callable $callback
     * @param string $errorMessage
     * @param string $errorCode
     * @param int $statusCode
     * @return mixed
     */
    public static function handleException(
        callable $callback,
        string $errorMessage = 'An error occurred',
        string $errorCode = 'SERVER_ERROR',
        int $statusCode = 500
    ) {
        try {
            return $callback();
        } catch (\Throwable $th) {
            return self::serverError(
                $errorMessage . ': ' . $th->getMessage(),
                $errorCode,
                [$th->getMessage()]
            );
        }
    }
}