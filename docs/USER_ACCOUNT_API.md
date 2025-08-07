# User Account API Documentation

## Table of Contents
- [Overview](#overview)
- [Registration & Authentication](#registration--authentication)
- [Profile Management](#profile-management)
- [Password Recovery](#password-recovery)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
Endpoints for managing user accounts including:
- Registration and login
- Profile management
- Password recovery
- Account status

## Registration & Authentication

### POST /api/auth/register
Register new account

#### Request:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securePassword123",
  "password_confirmation": "securePassword123"
}
```

### POST /api/auth/login
Authenticate user

#### Request:
```json
{
  "email": "john@example.com",
  "password": "securePassword123"
}
```

## Profile Management

### GET /api/user/profile
Get user profile

### GET /api/user/role
Check account status

## Password Recovery

### POST /api/auth/forgot-password
Request password reset

### POST /api/auth/reset-password
Complete password reset

## Status Codes
- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 422: Validation Error

## Error Handling
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
