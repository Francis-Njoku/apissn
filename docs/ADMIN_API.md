# Admin API Documentation

## Table of Contents

-   [Overview](#overview)
-   [User Management](#user-management)
-   [Article Management](#article-management)
-   [Media Management](#media-management)
-   [Payment Management](#payment-management)
-   [Comment Moderation](#comment-moderation)
-   [Stock Pick Management](#stock-pick-management)
-   [Status Codes](#status-codes)
-   [Error Handling](#error-handling)

## Overview

Endpoints for administrative functions requiring admin privileges. All endpoints require JWT authentication and admin role. This section provides comprehensive details for each admin API endpoint.

## User Management

### GET /api/admin/users

List all users (paginated). Retrieves a list of all users in the system, with options to filter by role and control pagination.

#### Query Parameters:

-   `role` (string, optional): Filter users by their role. Accepted values: `admin`, `user`.
-   `per_page` (integer, optional): Number of users to display per page. Defaults to 10.

#### Response:

Returns a paginated JSON object containing user data:

-   `data` (array): An array of user objects, each containing:
    -   `id` (integer): Unique user identifier.
    -   `name` (string): User's unique name/username.
    -   `email` (string): User's email address.
    -   `role_id` (integer): Identifier for the user's role (e.g., 1 for admin, 2 for user).
    -   `status` (string): The account status (e.g., 'approved', 'pending').
    -   `created_at` (string): Timestamp of account creation.
-   `meta` (object): Pagination metadata including `current_page`, `per_page`, and `total`.

```json
{
    "data": [
        {
            "id": 1,
            "name": "adminuser1",
            "email": "admin@example.com",
            "role_id": 1,
            "status": "approved",
            "created_at": "2025-06-15T08:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 50
    }
}
```

### POST /api/admin/users/create

Admin create new user. Allows administrators to create new user accounts with specified details and roles.

#### Request Body:

-   `first_name` (string, optional): First name of the new user.
-   `last_name` (string, optional): Last name of the new user.
-   `email` (string, required): Email address for the new user. Must be unique and a valid email format.
-   `password` (string, required): Password for the new user. Must meet complexity requirements.
-   `phone` (string, optional): Phone number for the new user.
-   `role_id` (integer, optional): The role ID for the new user (e.g., 1 for admin, 2 for user). Defaults to user role if not provided.

```json
{
    "first_name": "Admin",
    "last_name": "User",
    "email": "admin2@example.com",
    "password": "AdminSecurePassword123!",
    "phone": "1234567890",
    "role_id": 1
}
```

#### Response:

On successful creation, returns:

-   `status` (boolean): Indicates the success of the operation (true).
-   `message` (string): Confirmation message, e.g., "User Created Successfully".

```json
{
    "status": true,
    "message": "User Created Successfully"
}
```

## Article Management

### POST /api/admin/articles/add

Create new article. Administrators can use this endpoint to add new articles to the platform.

#### Request Body:

All fields are required unless otherwise specified.

-   `mediaType` (string): Type of media for the article (e.g., 'text', 'video', 'audio', 'bytes').
-   `news_type_id` (string): Identifier for the news category.
-   `name` (string): Internal name or title for the article.
-   `title` (string): The main title of the article displayed to users.
-   `news_date` (string): The publication date in 'YYYY-MM-DD' format.
-   `body` (string, optional): The main content of the article. Minimum 10 characters, maximum 100000 characters.
-   `featuredImage` (string): Path or URL to the featured image.
-   `status` (string): The publication status ('draft', 'published', 'archived', etc.).
-   `author_id` (integer): The ID of the author of the article.

```json
{
    "mediaType": "text",
    "news_type_id": "1",
    "name": "Internal Article Name",
    "title": "New Article Title",
    "news_date": "2025-07-01",
    "body": "This is the content of the new article. It should be detailed and informative.",
    "featuredImage": "https://example.com/storage/featured_image/new_article_image.jpg",
    "status": "draft",
    "author_id": 1
}
```

#### Response:

Returns the newly created article's basic information upon successful creation.

-   `status` (string): Indicates success or failure of the operation.
-   `data` (object): Contains basic details of the created article.
    -   `id` (integer): Unique identifier of the new article.
    -   `title` (string): The title of the new article.

```json
{
    "status": "success",
    "data": {
        "id": 42,
        "title": "New Article Title"
    }
}
```

### PUT /api/admin/articles/update/{slug}

Update existing article. Allows administrators to modify article details using the article's slug.

#### Parameters:

-   `slug` (string, required): The URL-friendly identifier of the article to update.

#### Request Body:

Fields to be updated. All fields are optional; provide only those that need modification.

-   `title` (string, optional): New title for the article.
-   `news_date` (string, optional): New publication date ('YYYY-MM-DD').
-   `body` (string, optional): New content for the article.
-   `featuredImage` (string, optional): New path or URL for the featured image.
-   `media` (string, optional): New media identifier.
-   `status` (string, optional): New publication status.
-   `tags` (string, optional): New comma-separated tags for the article.

```json
{
    "title": "Updated Article Title",
    "body": "This is the updated content of the article.",
    "status": "published"
}
```

#### Response:

Returns a success message and the updated article's basic information.

-   `message` (string): Confirmation message, e.g., "Article updated successfully!".
-   `data` (object): Basic details of the updated article.
    -   `id` (integer): Unique identifier of the updated article.
    -   `title` (string): The updated title of the article.

```json
{
    "message": "Article updated successfully!",
    "data": {
        "id": 42,
        "title": "Updated Article Title"
    }
}
```

## Media Management

### GET /api/admin/media

List all media files. Retrieves a list of all uploaded media files, including their names and URLs.

#### Response:

Returns a JSON array of media objects, each containing:

-   `file_name` (string): The name of the file, including its path relative to the storage directory.
-   `media_url` (string): The publicly accessible URL for the media file.

```json
{
    "data": [
        {
            "file_name": "featured_image/image_20250701.jpg",
            "media_url": "https://example.com/storage/featured_image/image_20250701.jpg"
        }
    ]
}
```

### POST /api/admin/media/upload

Upload media file. Allows administrators to upload files (images, videos, audio) to specified directories.

#### Request:

-   `file` (file, multipart/form-data, required): The media file to upload. Accepted MIME types: `image/jpeg`, `image/png`, `image/jpg`, `audio/mpeg`, `audio/x-wav`, `audio/mp3`, `video/avi`, `video/mpeg`, `video/quicktime`, `video/mp4`.
-   `folder` (string, optional): The directory within the storage path where the file should be uploaded. Defaults to 'all'.

#### Response:

On successful upload, returns:

-   `status` (string): Indicates the success of the operation ('success').
-   `data` (object): Information about the uploaded file:
    -   `directory` (string): The directory where the file was stored.
    -   `filename` (string): The name of the stored file.

```json
{
    "status": "success",
    "data": {
        "directory": "featured_image",
        "filename": "image_20250701.jpg"
    }
}
```

## Payment Management

### GET /api/admin/pay/all

List all payments with user details. Administrators can view all payment records across all users, including associated user information.

#### Response:

Returns a paginated list of payment records, including user details:

-   `data` (array): An array of payment objects, each containing:
    -   `id` (integer): Unique identifier for the payment record.
    -   `amount` (float): The amount paid.
    -   `user` (object): Information about the user who made the payment:
        -   `name` (string): The user's name.
        -   `email` (string): The user's email address.
    -   `status` (string): The status of the payment ('completed', 'pending', etc.).
    -   `created_at` (string): Timestamp of when the payment was made.
-   `meta` (object): Pagination metadata, including `current_page`, `per_page`, and `total`.

```json
{
    "data": [
        {
            "id": 1,
            "amount": 5000.0,
            "user": {
                "name": "John Doe",
                "email": "john.doe@example.com"
            },
            "status": "completed",
            "created_at": "2025-07-01T10:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

## Comment Moderation

### GET /api/admin/comments/pending

List pending comments for moderation. Retrieves comments that are awaiting moderation approval.

#### Response:

Returns a JSON array of comment objects, each containing:

-   `id` (integer): Unique identifier for the comment.
-   `content` (string): The text content of the comment.
-   `status` (string): The current moderation status (`pending`).
-   `user` (object, nullable): Information about the comment author if logged in.

```json
{
    "data": [
        {
            "id": 1,
            "content": "Pending comment awaiting review.",
            "status": "pending",
            "user": {
                "name": "John Doe"
            }
        }
    ]
}
```

### PATCH /api/admin/comments/{comment}/moderate

Moderate comment. Allows administrators or moderators to change the status of a comment.

#### Parameters:

-   `comment` (integer, required): The ID of the comment to moderate.

#### Request Body:

-   `status` (string, required): The new moderation status for the comment. Accepted values: `approved`, `rejected`, `spam`.

```json
{
    "status": "approved"
}
```

## Stock Pick Management

### POST /api/admin/stockpicks

Create stock pick recommendation. Allows administrators to add new stock recommendations to the system.

#### Request Body:

-   `symbol` (string, required): The stock ticker symbol. Max 10 characters.
-   `newsletter_id` (integer, optional): The ID of the associated newsletter. Must exist in the `newsletter` table if provided.
-   `recommendation_date` (string, required): The date of recommendation in 'YYYY-MM-DD' format.
-   `initial_price` (float, required): The stock price at the time of recommendation.

```json
{
    "symbol": "AAPL",
    "newsletter_id": 42,
    "recommendation_date": "2025-07-01",
    "initial_price": 185.24
}
```

### PATCH /api/admin/stockpicks/{stockPick}/price

Update stock price. Allows administrators to quickly update the current price of a stock pick.

#### Parameters:

-   `stockPick` (integer, required): The ID of the stock pick to update.

#### Request Body:

-   `current_price` (float, required): The new current market price of the stock.

```json
{
    "current_price": 210.5
}
```

## Status Codes

-   `200 OK`: Request successful.
-   `201 Created`: Resource successfully created.
-   `400 Bad Request`: The request was malformed or invalid (e.g., missing required fields, invalid data format).
-   `401 Unauthorized`: Authentication failed. The provided token is missing, invalid, or expired.
-   `403 Forbidden`: The authenticated user does not have the necessary permissions (e.g., not an admin).
-   `404 Not Found`: The requested resource (e.g., user, article, comment, stock pick) could not be found.
-   `422 Unprocessable Entity`: The request was valid but contained semantic errors, typically validation failures.

## Error Handling

Provides details about validation errors or other issues encountered during request processing.

```json
{
    "status": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password must be at least 8 characters long."]
    }
}
```
