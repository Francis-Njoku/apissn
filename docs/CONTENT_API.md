# Content API Documentation

## Table of Contents

-   [Overview](#overview)
-   [Public Endpoints](#public-endpoints)
-   [Subscriber Endpoints](#subscriber-endpoints)
-   [Admin Endpoints](#admin-endpoints)
-   [Status Codes](#status-codes)
-   [Error Handling](#error-handling)

## Overview

Endpoints for managing and accessing content including articles, media, and related operations. Requires JWT authentication for most endpoints.

## Public Endpoints

No authentication required.

### GET /api/articles/sample/

Get sample articles for unsubscribed users. This endpoint retrieves a limited set of articles, typically for preview purposes.

#### Parameters:

-   `m` (string, optional): Filter articles by media type. Accepted values are `text`, `audio`, `video`, `bytes`. If not provided, it defaults to filtering by `text`.
-   `n` (integer, optional): Filter articles by their news type ID. This parameter allows for fetching articles belonging to a specific category.

#### Response Format:

Returns a JSON array of article objects, each containing:

-   `id` (integer): Unique identifier for the article.
-   `title` (string): The title of the article.
-   `mediaType` (string): The type of media associated with the article (e.g., 'text', 'video').
-   `news_date` (string): The publication date of the article in 'YYYY-MM-DD' format.
-   `featuredImage` (string, nullable): The URL or path to the featured image for the article.

```json
{
    "data": [
        {
            "id": 1,
            "title": "Sample Article Title",
            "mediaType": "text",
            "news_date": "2025-07-01",
            "featuredImage": "https://example.com/storage/featured_image/sample_image.jpg"
        }
    ]
}
```

#### Example:

```bash
curl -X GET "https://api.example.com/api/articles/sample/?m=video&n=5"
```

### GET /api/plans/

List all available subscription plans. This endpoint provides details about different subscription tiers offered by the service.

#### Response Format:

Returns a JSON array of plan objects, each containing:

-   `id` (integer): Unique identifier for the subscription plan.
-   `name` (string): The name of the plan (e.g., "Basic", "Premium").
-   `price` (float): The cost of the subscription plan.
-   `features` (array of strings): A list of features included in the plan.

```json
{
    "data": [
        {
            "id": 1,
            "name": "Basic Plan",
            "price": 9.99,
            "features": ["Access to all articles", "Email notifications"]
        },
        {
            "id": 2,
            "name": "Premium Plan",
            "price": 19.99,
            "features": [
                "All Basic features",
                "Ad-free experience",
                "Exclusive content"
            ]
        }
    ]
}
```

## Subscriber Endpoints

Requires valid JWT token and active subscription.

### GET /api/articles/

List all articles with filtering and pagination options. This is the primary endpoint for authenticated users to browse articles.

#### Parameters:

-   `s` (string, optional): Search term to filter articles by title or content.
-   `m` (string, optional): Filter articles by media type (`text`, `audio`, `video`, `bytes`).
-   `n` (integer, optional): Filter articles by news type ID.
-   `page` (integer, optional): The page number for pagination. Defaults to 1.
-   `per_page` (integer, optional): The number of articles to return per page. Defaults to 10, with a maximum of 100.
-   `sort_by` (string, optional): Field to sort by. Accepted values: `title`, `news_date`, `created_at`. Defaults to `news_date`.
-   `sort_order` (string, optional): Sort order. Accepted values: `asc`, `desc`. Defaults to `desc`.

#### Response Format:

Returns a paginated JSON object containing:

-   `data` (array): An array of article objects, each containing:
    -   `id` (integer): Unique identifier for the article.
    -   `slug` (string): URL-friendly identifier for the article.
    -   `title` (string): The title of the article.
    -   `mediaType` (string): The type of media associated with the article.
    -   `news_date` (string): The publication date of the article.
    -   `status` (string): The publication status of the article (e.g., 'published', 'draft').
-   `meta` (object): Pagination metadata including:
    -   `current_page` (integer): The current page number.
    -   `per_page` (integer): The number of items per page.
    -   `total` (integer): The total number of articles available.

```json
{
    "data": [
        {
            "id": 1,
            "slug": "article-slug-1",
            "title": "Article Title One",
            "mediaType": "text",
            "news_date": "2025-07-01",
            "status": "published"
        },
        {
            "id": 2,
            "slug": "article-slug-2",
            "title": "Article Title Two",
            "mediaType": "video",
            "news_date": "2025-06-28",
            "status": "published"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 100
    }
}
```

### GET /api/articles/{slug}/

Get a single article by its slug. This endpoint retrieves detailed information about a specific article, including its content and associated comments.

#### Response Format:

Returns a JSON object representing the article, including:

-   `id` (integer): Unique identifier for the article.
-   `title` (string): The title of the article.
-   `body` (string): The full content of the article.
-   `mediaType` (string): The type of media associated with the article.
-   `featuredImage` (string, nullable): URL or path to the featured image.
-   `comments_count` (integer): The total number of approved comments for the article.
-   `comments` (array): An array of approved comment objects, each containing:
    -   `id` (integer): Unique identifier for the comment.
    -   `body` (string): The content of the comment.
    -   `user` (object): Information about the comment author:
        -   `name` (string): The name of the user.

```json
{
    "id": 1,
    "title": "Detailed Article Title",
    "body": "This is the full content of the article. It can be quite lengthy and may include various sections and details.",
    "mediaType": "text",
    "featuredImage": "https://example.com/storage/featured_image/article_image.jpg",
    "comments_count": 5,
    "comments": [
        {
            "id": 1,
            "body": "This is the first approved comment.",
            "user": {
                "name": "John Doe"
            }
        },
        {
            "id": 2,
            "body": "Another insightful comment.",
            "user": {
                "name": "Jane Smith"
            }
        }
    ]
}
```

### GET /api/articles/search

Search articles by title, content, or tags. This endpoint allows users to find relevant articles based on keywords.

#### Parameters:

-   `q` (string, required): The search query string.
-   `limit` (integer, optional): The maximum number of search results to return. Defaults to 10.

#### Response Format:

Returns a JSON array of article summary objects, each containing:

-   `id` (integer): Unique identifier for the article.
-   `slug` (string): URL-friendly identifier for the article.
-   `title` (string): The title of the article.
-   `mediaType` (string): The type of media associated with the article.

```json
[
    {
        "id": 1,
        "slug": "article-slug-1",
        "title": "Article Title Matching Query",
        "mediaType": "text"
    },
    {
        "id": 5,
        "slug": "another-article-slug",
        "title": "Another Article Title",
        "mediaType": "video"
    }
]
```

## Admin Endpoints

Requires admin privileges.

### POST /admin/articles/add

Create a new article. This endpoint is used by administrators to add new content to the platform.

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
        "id": 1,
        "title": "New Article Title"
    }
}
```

### PUT /admin/articles/update/{slug}

Update an existing article identified by its slug. Allows administrators to modify article details.

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
    "status": "published",
    "tags": "finance, economy, market"
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
        "id": 1,
        "title": "Updated Article Title"
    }
}
```

### GET /admin/media

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

### POST /admin/media/upload

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

### GET /admin/articles/update-image-paths

Updates the `featuredImage` paths for all articles. This is a utility endpoint to correct image paths if they were stored incorrectly.

#### Response:

A simple string message indicating the success of the operation.

```
Image paths updated successfully!
```

## Status Codes

-   `200 OK`: Request successful.
-   `201 Created`: Resource successfully created.
-   `400 Bad Request`: The request was malformed or invalid.
-   `401 Unauthorized`: Authentication failed. The provided token is missing, invalid, or expired.
-   `403 Forbidden`: The authenticated user does not have the necessary permissions.
-   `404 Not Found`: The requested resource could not be found.
-   `422 Unprocessable Entity`: The request was valid but contained semantic errors, typically validation failures.

## Error Handling

Provides details about validation errors or other issues encountered during request processing.

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "title": ["The title field is required."],
        "body": ["The body must be at least 10 characters long."]
    }
}
```
