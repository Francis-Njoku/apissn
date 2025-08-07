# Community API Documentation

## Table of Contents
- [Overview](#overview)
- [Viewing Comments](#viewing-comments)
- [Posting Comments](#posting-comments)
- [Managing Comments](#managing-comments)
- [Reporting Comments](#reporting-comments)
- [Moderating Comments](#moderating-comments)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
Endpoints for community interaction features including:
- Viewing and posting comments
- Managing user's own comments
- Reporting inappropriate content
- Moderating comments (admin/moderators)

## Viewing Comments

### GET /api/public/comments
List comments for a newsletter

#### Parameters:
- `newsletter_id` - Newsletter ID (required)
- `sort` - Sort order (newest, oldest)

## Posting Comments

### POST /api/comments
Create new comment

#### Request:
```json
{
  "newsletter_id": 42,
  "content": "This is my comment",
  "parent_id": 1 // optional, for replies
}
```

## Managing Comments

### PUT /api/comments/{comment}
Update comment

### DELETE /api/comments/{comment}
Delete comment

## Reporting Comments

### POST /api/comments/{comment}/report
Report a comment

### DELETE /api/comments/{comment}/report
Remove report

## Moderating Comments

### PATCH /api/comments/{comment}/moderate
Moderate comment (admin only)

### PATCH /api/comments/bulk-moderate
Bulk moderate comments (admin only)

## Status Codes
- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found

## Error Handling
```json
{
  "message": "Comment not found"
}
