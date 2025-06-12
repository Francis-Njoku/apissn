# Comments API Documentation

## Table of Contents
- [Overview](#overview)
- [Public Endpoints](#public-endpoints)
- [Authenticated Endpoints](#authenticated-endpoints)
- [Admin/Moderator Endpoints](#adminmoderator-endpoints)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
The Comments API allows users to:
- View comments on newsletters
- Create new comments
- Edit/delete their own comments
- Report inappropriate comments

Admin/moderators can:
- Moderate comments (approve/reject/mark as spam)
- View moderation statistics
- Bulk moderate comments

## Public Endpoints

### Get Comments
`GET /public/comments` (under /api base path)

Returns approved comments for a newsletter.

#### Parameters:
```json
{
  "newsletter_id": "required|integer|exists:newsletters,id",
  "per_page": "optional|integer|min:1|max:100",
  "sort": "optional|in:newest,oldest"
}
```

#### Example Response:
```json
{
  "data": [
    {
      "id": 1,
      "content": "Great article!",
      "status": "approved",
      "author": {
        "id": 123,
        "name": "John Doe"
      },
      "newsletter": {
        "id": 42
      },
      "replies": [
        {
          "id": 2,
          "content": "I agree!",
          "author": {
            "id": 456,
            "name": "Jane Smith"
          }
        }
      ]
    }
  ],
  "meta": {
    "total": 15
  }
}
```

## Authenticated Endpoints

### Create Comment
`POST /api/comments`

Create a new comment. Requires authentication.

#### Request:
```json
{
  "newsletter_id": 42,
  "content": "This is my comment",
  "parent_id": 1 // optional, for replies
}
```

#### Success Response (201):
```json
{
  "message": "Comment created successfully",
  "data": {
    "id": 3,
    "content": "This is my comment",
    "status": "pending",
    "author": {
      "id": 789,
      "name": "Current User"
    }
  }
}
```

### Update Comment
`PUT /api/comments/{comment}`

Update a comment. Must be owner or admin.

#### Request:
```json
{
  "content": "Updated comment text"
}
```

## Admin/Moderator Endpoints

### Moderate Comment
`PATCH /api/admin/comments/{comment}/moderate`

Moderate a single comment.

#### Request:
```json
{
  "status": "approved",
  "reason": "Meets guidelines"
}
```

### Bulk Moderate
`PATCH /api/admin/comments/bulk-moderate`

Moderate multiple comments at once.

#### Request:
```json
{
  "comment_ids": [1, 2, 3],
  "status": "rejected",
  "reason": "Spam content"
}
```

## Status Codes
- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error

## Error Handling
Errors return a JSON response with details:

```json
{
  "message": "Validation failed",
  "errors": {
    "newsletter_id": ["The newsletter id field is required."]
  }
}
