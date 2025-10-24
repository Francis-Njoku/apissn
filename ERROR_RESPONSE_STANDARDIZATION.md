# Error Response Standardization

## Overview

This document describes the standardized error response format implemented across all API endpoints in the FTM application. The standardization ensures consistent error handling while preserving all existing success response formats.

## Standardized Error Response Format

All error responses now follow this consistent structure:

```json
{
  "success": false,
  "code": "ERROR_CODE",
  "message": "Human-readable error description",
  "errors": {
    "field_name": ["Error message for this field"]
  },
  "data": null
}
```

### Field Descriptions

- **success**: Always `false` for error responses
- **code**: A machine-readable error code (string or number)
- **message**: A human-readable description of the error
- **errors**: An object containing validation errors (optional, null for non-validation errors)
- **data**: Always `null` for error responses

## HTTP Status Codes

| Status Code | Use Case |
|-------------|----------|
| 400 | Bad Request (general validation errors) |
| 401 | Unauthorized (authentication/authorization errors) |
| 403 | Forbidden (permission errors) |
| 404 | Not Found (resource not found) |
| 409 | Conflict (business logic conflicts) |
| 422 | Validation Error (request validation failed) |
| 500 | Internal Server Error (unexpected errors) |

## Error Response Types

### 1. Validation Errors
```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  },
  "data": null
}
```

### 2. Not Found Errors
```json
{
  "success": false,
  "code": "NOT_FOUND",
  "message": "Resource not found",
  "errors": null,
  "data": null
}
```

### 3. Unauthorized Errors
```json
{
  "success": false,
  "code": "UNAUTHORIZED",
  "message": "Unauthorized",
  "errors": null,
  "data": null
}
```

### 4. Server Errors
```json
{
  "success": false,
  "code": "SERVER_ERROR",
  "message": "Internal server error",
  "errors": ["Detailed error message for debugging"],
  "data": null
}
```

## Implementation Details

### Helper Classes

1. **ErrorResponse** (`app/Http/Responses/ErrorResponse.php`)
   - Provides static methods for creating standardized error responses
   - Includes methods for common error types (validation, not found, unauthorized, etc.)

2. **ApiResponseHelper** (`app/Helpers/ApiResponseHelper.php`)
   - Provides convenient helper functions for error responses
   - Includes validation helper and exception handling utilities

### Updated Controllers

All API controllers have been updated to use the standardized error response format:

- **ArticleController**: Updated search validation, validation errors, and not found responses
- **Auth/UserController**: Updated authentication errors, validation errors, and server errors
- **PayController**: Updated payment initiation and payment record errors
- **CommentController**: Updated validation errors
- **CommentModerationController**: Updated validation errors
- **CommentReportController**: Updated duplicate report error

## Success Response Preservation

All existing success response formats have been preserved exactly as they were. Only error responses have been standardized to ensure consistency.

### Examples of Preserved Success Responses

```json
// ArticleController - Success response (unchanged)
{
  "status": "Successful",
  "message": "Successfully updated slug"
}

// Auth/UserController - Success response (unchanged)
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}

// StockPickController - Success response (unchanged)
[
  {
    "id": 1,
    "symbol": "AAPL",
    "newsletter": {
      "id": 1,
      "slug": "example-article",
      "mediaType": "text"
    }
  }
]
```

## Migration Benefits

1. **Consistency**: All error responses now follow the same structure
2. **Predictability**: Frontend developers can expect consistent error formats
3. **Debugging**: Error codes and detailed error messages improve debugging
4. **Maintainability**: Centralized error handling makes maintenance easier
5. **Backward Compatibility**: All existing success responses remain unchanged

## Error Code Reference

| Error Code | Description | HTTP Status |
|------------|-------------|-------------|
| VALIDATION_ERROR | Request validation failed | 422 |
| NOT_FOUND | Resource not found | 404 |
| UNAUTHORIZED | Authentication required | 401 |
| INVALID_CREDENTIALS | Invalid login credentials | 401 |
| TOKEN_NOT_PROVIDED | JWT token not provided | 401 |
| TOKEN_CREATION_ERROR | JWT token creation failed | 500 |
| TOKEN_REFRESH_ERROR | JWT token refresh failed | 500 |
| LOGOUT_ERROR | Logout process failed | 500 |
| USER_NOT_FOUND | User not found | 404 |
| INVALID_TIMEZONE | Invalid timezone provided | 500 |
| USER_UPDATE_ERROR | User update failed | 500 |
| PAYMENT_INITIATION_ERROR | Payment initiation failed | 500 |
| PAYMENT_RECORD_ERROR | Payment record creation failed | 500 |
| ALREADY_REPORTED | Already reported this comment | 409 |
| SEARCH_QUERY_REQUIRED | Search query is required | 400 |
| NO_SAMPLE_ARTICLES | No sample articles available | 404 |
| FORBIDDEN | Access forbidden | 403 |
| SERVER_ERROR | General server error | 500 |

## Testing

All error responses have been tested to ensure:
- Consistent structure across all endpoints
- Correct HTTP status codes
- Proper error codes and messages
- Preservation of all success response formats