# User Account API Documentation

## Table of Contents

-   [Overview](#overview)
-   [Public Endpoints](#public-endpoints)
-   [Authenticated Endpoints](#authenticated-endpoints)
-   [Admin Endpoints](#admin-endpoints)
-   [Status Codes](#status-codes)
-   [Error Handling](#error-handling)

## Overview

Endpoints for managing user authentication, profiles, and account operations. Uses JWT for authentication. This section provides comprehensive details for each user account API endpoint.

## Public Endpoints

No authentication required.

### POST /api/auth/register

Register new user account. This endpoint allows new users to create an account by providing their details.

#### Request Body:

-   `first_name` (string, optional): The user's first name.
-   `last_name` (string, optional): The user's last name.
-   `email` (string, required): The user's email address. Must be unique and a valid email format.
-   `password` (string, required): The user's password. Must meet complexity requirements (e.g., minimum length, character types).
-   `phone` (string, optional): The user's phone number.

```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "password": "SecurePassword123!",
    "phone": "123-456-7890"
}
```

#### Response:

On successful registration, returns:

-   `status` (boolean): Indicates the success of the operation (true).
-   `message` (string): A confirmation message like "User Created Successfully".

```json
{
    "status": true,
    "message": "User Created Successfully"
}
```

### POST /api/auth/login

Authenticate user and return JWT tokens. This endpoint validates user credentials and issues authentication tokens upon success.

#### Request Body:

-   `email` (string, required): The user's registered email address.
-   `password` (string, required): The user's password.

```json
{
    "email": "john.doe@example.com",
    "password": "SecurePassword123!"
}
```

#### Response:

On successful authentication, returns:

-   `token` (string): The JWT access token for authenticating subsequent requests.
-   `refresh_token` (string): A token used to obtain a new access token.
-   `group_id` (integer): The user's role identifier (e.g., 2 for regular users).

```json
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "group_id": 2
}
```

### POST /api/auth/forgot-password

Request password reset email. Sends a verification code to the user's email address to initiate the password reset process.

#### Request Body:

-   `email` (string, required): The email address associated with the user's account.

```json
{
    "email": "john.doe@example.com"
}
```

#### Response:

-   `success` (boolean): Indicates if the request was processed successfully.
-   `message` (string): Instructions for the user, e.g., "Please check your email for a 6 digit pin".

```json
{
    "success": true,
    "message": "Please check your email for a 6 digit pin"
}
```

### POST /api/auth/reset-password

Reset password using verification token. Allows users to set a new password after verifying their identity with a code sent via email.

#### Request Body:

-   `email` (string, required): The user's email address.
-   `token` (string, required): The verification token received via email.
-   `password` (string, required): The new password for the account. Must meet complexity requirements.
-   `password_confirmation` (string, required): Confirmation of the new password. Must match the `password` field.

```json
{
    "email": "john.doe@example.com",
    "token": "123456",
    "password": "NewSecurePassword123!",
    "password_confirmation": "NewSecurePassword123!"
}
```

#### Response:

-   `success` (boolean): Indicates if the password reset was successful.
-   `message` (string): Confirmation message, e.g., "Your password has been reset".

```json
{
    "success": true,
    "message": "Your password has been reset"
}
```

## Authenticated Endpoints

Requires valid JWT token.

### POST /api/auth/logout

Invalidate current JWT token. This logs the user out by invalidating their active session token.

#### Response:

-   `message` (string): Confirmation message, e.g., "Successfully logged out".

```json
{
    "message": "Successfully logged out"
}
```

### POST /api/auth/signout

Alternative logout endpoint. Also invalidates the current JWT token.

#### Response:

-   `message` (string): Confirmation message, e.g., "Successfully logged out".

```json
{
    "message": "Successfully logged out"
}
```

### GET /api/user/profile

Get authenticated user's profile. Retrieves detailed information about the currently logged-in user.

#### Response:

Returns a JSON object with the user's profile details:

-   `id` (integer): Unique user identifier.
-   `name` (string): User's unique name/username.
-   `email` (string): User's email address.
-   `first_name` (string, nullable): User's first name.
-   `last_name` (string, nullable): User's last name.
-   `phone` (string, nullable): User's phone number.
-   `identity` (string): A unique identity string for the user.
-   `role_id` (integer): Identifier for the user's role (e.g., 1 for admin, 2 for user).
-   `status` (string): The account status (e.g., 'approved', 'pending').
-   `created_at` (string): Timestamp of account creation.

```json
{
    "id": 1,
    "name": "johndoe123",
    "email": "john.doe@example.com",
    "first_name": "John",
    "last_name": "Doe",
    "phone": "123-456-7890",
    "identity": "1234567890123456",
    "role_id": 2,
    "status": "approved",
    "created_at": "2025-01-15T10:00:00Z"
}
```

### GET /api/user/role

Check user's role/status. Determines if the user has admin privileges or is a standard user.

#### Response:

-   `status` (string): Indicates the user's role ('admin' or 'user').
-   `message` (string): A descriptive message about the user's status.

```json
{
    "status": "user",
    "message": "User is a standard user"
}
```

## Admin Endpoints

Requires admin privileges.

### POST /api/admin/users/create

Admin create new user. Allows administrators to create new user accounts with specified details and roles.

#### Request Body:

-   `first_name` (string, optional): First name of the new user.
-   `last_name` (string, optional): Last name of the new user.
-   `email` (string, required): Email address for the new user. Must be unique.
-   `password` (string, required): Password for the new user.
-   `phone` (string, optional): Phone number for the new user.
-   `role_id` (integer, optional): The role ID for the new user (e.g., 1 for admin, 2 for user). Defaults to user role if not provided.

```json
{
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane.smith@example.com",
    "password": "AdminSecurePassword123!",
    "phone": "987-654-3210",
    "role_id": 1
}
```

#### Response:

-   `status` (boolean): Indicates the success of the operation (true).
-   `message` (string): Confirmation message, e.g., "User Created Successfully".

```json
{
    "status": true,
    "message": "User Created Successfully"
}
```

### GET /api/admin/users/

List all users (paginated). Provides a paginated list of all users in the system, with filtering options.

#### Query Parameters:

-   `role` (string, optional): Filter users by their role. Accepted values: `admin`, `user`.
-   `per_page` (integer, optional): Number of users to display per page. Defaults to 10.

#### Response:

Returns a paginated JSON object containing user data:

-   `data` (array): An array of user objects, each containing:
    -   `id` (integer): Unique user identifier.
    -   `name` (string): User's unique name/username.
    -   `email` (string): User's email address.
    -   `role_id` (integer): Identifier for the user's role.
    -   `status` (string): The account status.
    -   `created_at` (string): Timestamp of account creation.
-   `meta` (object): Pagination metadata including:
    -   `current_page` (integer): The current page number.
    -   `per_page` (integer): The number of items per page.
    -   `total` (integer): The total number of users.

```json
{
    "data": [
        {
            "id": 1,
            "name": "johndoe123",
            "email": "john.doe@example.com",
            "role_id": 2,
            "status": "approved",
            "created_at": "2025-01-15T10:00:00Z"
        },
        {
            "id": 2,
            "name": "adminsmith",
            "email": "jane.smith@example.com",
            "role_id": 1,
            "status": "approved",
            "created_at": "2025-07-01T12:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 50
    }
}
```

## Status Codes

-   `200 OK`: Request successful.
-   `201 Created`: Resource successfully created.
-   `400 Bad Request`: The request was malformed or invalid (e.g., invalid email format, expired token).
-   `401 Unauthorized`: Authentication failed. The provided token is missing, invalid, or expired.
-   `403 Forbidden`: The authenticated user does not have the necessary permissions to perform the action.
-   `404 Not Found`: The requested resource (e.g., user, token) could not be found.
-   `422 Unprocessable Entity`: The request was valid but contained semantic errors, typically validation failures.

## Error Handling

Provides details about validation errors or other issues encountered during request processing.

```json
{
    "status": false,
    "message": "validation error",
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password must be at least 8 characters long."]
    }
}
```
