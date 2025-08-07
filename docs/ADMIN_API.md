# Admin API Documentation

## Table of Contents
- [Overview](#overview)
- [System Administration](#system-administration)
- [User Management](#user-management)
- [Content Management](#content-management)
- [Media Management](#media-management)
- [System Monitoring](#system-monitoring)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
The Admin API provides endpoints for core administrative functions including:
- System configuration and monitoring
- User account administration
- Content management
- Media library administration

Requires:
- JWT authentication (`auth.jwt` middleware)
- Admin privileges (`admin` middleware)

## System Administration

### GET /api/admin/system/status
Check system health status

#### Success Response:
```json
{
  "status": "healthy",
  "services": {
    "database": true,
    "cache": true,
    "mail": true
  }
}
```

## User Management

### GET /api/admin/users
List all users

#### Success Response:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "role": "admin",
      "created_at": "2025-06-15T08:30:00Z"
    }
  ]
}
```

### POST /api/admin/users/create
Create new user account

## Content Management

### POST /api/admin/articles/add
Create new article

### PUT /api/admin/articles/update/{slug}
Update existing article

### PUT /api/admin/articles/status/update/{slug}
Update article status

## Media Management

### POST /api/admin/media/upload
Upload media file

### GET /api/admin/media
List media files

## System Monitoring

### GET /api/admin/system/logs
View system logs

### GET /api/admin/system/stats
View system statistics

## Status Codes
- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error

## Error Handling
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
