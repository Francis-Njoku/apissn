# Community API Documentation

## Table of Contents

-   [Overview](#overview)
-   [Comment Listing](#comment-listing)
-   [Comment Creation](#comment-creation)
-   [Comment Management](#comment-management)
-   [Status Codes](#status-codes)
-   [Error Handling](#error-handling)

## Overview

Endpoints for managing comments on newsletter articles. Includes moderation capabilities for admins/moderators. This section details how users can view, post, and manage comments, as well as how administrators can moderate them.

## Comment Listing

### GET /api/comments

List comments for a newsletter with filtering and sorting options. This endpoint retrieves comments associated with a specific article, applying filters and sorting as requested.

#### Query Parameters:

-   `newsletter_id` (integer, required): The ID of the newsletter or article for which to retrieve comments.
-   `status` (string, optional): Filter comments by their status. Accepted values: `pending`, `approved`, `rejected`, `spam`.
-   `per_page` (integer, optional): The number of comments to return per page. Defaults to 10, with a maximum of 100.
-   `sort` (string, optional): The order in which to sort comments. Accepted values: `newest`, `oldest`. Defaults to `newest`.

#### Response:

Returns a paginated JSON object containing comments, structured as a thread:

-   `data` (array): An array of top-level comment objects, each containing:
    -   `id` (integer): Unique identifier for the comment.
    -   `content` (string): The text content of the comment.
    -   `status` (string): The moderation status of the comment (`pending`, `approved`, `rejected`, `spam`).
    -   `created_at` (string): Timestamp of when the comment was posted.
    -   `user` (object, nullable): Information about the comment author if logged in:
        -   `id` (integer): Unique user identifier.
        -   `name` (string): The user's name.
    *   `author_name` (string, nullable): The name of the guest author if the comment was posted by a guest.
    -   `replies` (array): An array of reply objects, structured similarly to top-level comments, representing nested comments.
-   `meta` (object): Metadata for pagination:
    -   `total` (integer): The total number of comments retrieved based on the query.

```json
{
    "data": [
        {
            "id": 1,
            "content": "Great article! Very informative.",
            "status": "approved",
            "created_at": "2025-07-01T10:00:00Z",
            "user": {
                "id": 1,
                "name": "John Doe"
            },
            "author_name": null,
            "replies": [
                {
                    "id": 2,
                    "content": "I agree! Thanks for sharing.",
                    "status": "approved",
                    "created_at": "2025-07-01T11:00:00Z",
                    "user": {
                        "id": 2,
                        "name": "Jane Smith"
                    },
                    "author_name": null,
                    "replies": []
                }
            ]
        }
    ],
    "meta": {
        "total": 15
    }
}
```

## Comment Creation

### POST /api/comments

Create new comment or reply. This endpoint allows authenticated users or guests to post comments on articles.

#### Request Body:

-   `newsletter_id` (integer, required): The ID of the newsletter or article to comment on. Must exist in the `newsletter` table.
-   `content` (string, required): The text content of the comment. Must be between 3 and 1000 characters.
-   `parent_id` (integer, optional): If this is a reply to an existing comment, provide the ID of the parent comment.
-   `author_name` (string, required if `user_id` is not present): The name of the guest author. Maximum 255 characters.
-   `author_email` (string, required if `user_id` is not present): The email of the guest author. Must be a valid email format.

```json
{
    "newsletter_id": 42,
    "content": "This is a very insightful comment about the article.",
    "parent_id": 1,
    "author_name": "Guest Commenter",
    "author_email": "guest@example.com"
}
```

#### Response:

On successful creation, returns:

-   `message` (string): Confirmation message, e.g., "Comment created successfully".
-   `data` (object): The newly created comment object, including:
    -   `id` (integer): Unique identifier for the new comment.
    -   `content` (string): The content of the comment.
    -   `status` (string): The initial moderation status (`pending` or `approved`).
    -   `created_at` (string): Timestamp of comment creation.
    -   `user` (object, nullable): User details if the comment was posted by a logged-in user.
    -   `author_name` (string, nullable): The name of the guest author if applicable.

```json
{
    "message": "Comment created successfully",
    "data": {
        "id": 3,
        "content": "This is a very insightful comment about the article.",
        "status": "pending",
        "created_at": "2025-07-02T10:00:00Z",
        "user": null,
        "author_name": "Guest Commenter"
    }
}
```

## Comment Management

### GET /api/comments/{comment}

Get single comment details. Retrieves a specific comment by its ID, including its author and replies.

#### Parameters:

-   `comment` (integer, required): The ID of the comment to retrieve.

#### Response:

Returns the requested comment object, including author details and nested replies.

-   `data` (object): The comment object, similar in structure to the response of `GET /api/comments`.

```json
{
    "data": {
        "id": 1,
        "content": "Great article! Very informative.",
        "status": "approved",
        "created_at": "2025-07-01T10:00:00Z",
        "user": {
            "id": 1,
            "name": "John Doe"
        },
        "author_name": null,
        "replies": []
    }
}
```

### PUT /api/comments/{comment}

Update comment content. Allows users to edit their own comments or allows admins/moderators to edit any comment.

#### Parameters:

-   `comment` (integer, required): The ID of the comment to update.

#### Request Body:

-   `content` (string, required): The new content for the comment. Must be between 3 and 1000 characters.

```json
{
    "content": "This is my updated and improved comment."
}
```

#### Response:

Returns a success message and the updated comment object. The comment's status is reset to 'pending' after an edit.

-   `message` (string): Confirmation message, e.g., "Comment updated successfully".
-   `data` (object): The updated comment object.

```json
{
    "message": "Comment updated successfully",
    "data": {
        "id": 1,
        "content": "This is my updated and improved comment.",
        "status": "pending",
        "created_at": "2025-07-01T10:00:00Z",
        "user": {
            "id": 1,
            "name": "John Doe"
        },
        "author_name": null,
        "replies": []
    }
}
```

### DELETE /api/comments/{comment}

Delete a comment. Allows users to delete their own comments or allows admins/moderators to delete any comment.

#### Parameters:

-   `comment` (integer, required): The ID of the comment to delete.

#### Response:

-   `message` (string): Confirmation message, e.g., "Comment deleted successfully".

```json
{
    "message": "Comment deleted successfully"
}
```

## Status Codes

-   `200 OK`: Request successful.
-   `201 Created`: Comment successfully created.
-   `400 Bad Request`: The request was malformed or invalid (e.g., missing `newsletter_id`, invalid `status` value).
-   `401 Unauthorized`: Authentication failed. The provided token is missing, invalid, or expired.
-   `403 Forbidden`: The authenticated user does not have the necessary permissions to perform the action (e.g., editing/deleting another user's comment without admin rights).
-   `404 Not Found`: The requested comment or newsletter could not be found.
-   `422 Unprocessable Entity`: The request was valid but contained semantic errors, typically validation failures (e.g., comment content too short or too long).

## Error Handling

Provides details about validation errors or other issues encountered during request processing.

```json
{
    "errors": {
        "content": ["The content must be at least 3 characters."],
        "newsletter_id": ["The newsletter_id must be an integer and exist."]
    }
}
```
