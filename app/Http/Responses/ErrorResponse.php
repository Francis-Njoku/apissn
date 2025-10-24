<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ErrorResponse
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
    public static function create(
        string $message,
        $code = 'ERROR',
        ?array $errors = null,
        int $statusCode = 400
    ): JsonResponse {
        $response = [
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
            'data' => null,
        ];

        return response()->json($response, $statusCode);
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
        return self::create($message, 'VALIDATION_ERROR', $errors, $statusCode);
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
        return self::create($message, $code, null, 404);
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
        return self::create($message, $code, null, 401);
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
        return self::create($message, $code, $errors, 500);
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
        return self::create($message, $code, null, 403);
    }
}