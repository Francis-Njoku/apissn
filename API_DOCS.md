# API Reference

This document provides a single, harmonized reference for your APIs with consistent structure, terminology, and examples. It consolidates User Account, Subscription, Content, Investment, Community, Admin, and Admin Metrics endpoints.

-   Base path: /api
-   Content type: application/json
-   Authentication: JWT via Authorization: Bearer <token>

## Table of Contents

-   Overview
-   Authentication
-   Conventions
-   User Account API
-   Subscription API
-   Content API
-   Investment API
-   Community API
-   Admin API
    -   User Management
    -   Article Management
    -   Media Management
    -   Payment Management
    -   Comment Moderation
    -   Stock Pick Management
-   Admin Metrics API
-   Status Codes
-   Error Handling

## Overview

This API powers authentication, subscription billing, content delivery, investments (stock picks), community comments, and administrative operations. Unless stated otherwise:

-   All write operations require authentication.
-   Admin endpoints require the admin role.
-   All dates and times are ISO 8601 unless specified.

## Error Response Standardization

All API endpoints now return standardized error responses while preserving existing success response formats. This ensures consistent error handling across the entire API.

### Key Benefits

- **Consistency**: All error responses follow the same structure
- **Predictability**: Frontend developers can expect consistent error formats
- **Backward Compatibility**: All existing success responses remain unchanged
- **Better Debugging**: Error codes and detailed error messages improve debugging
- **Maintainability**: Centralized error handling makes maintenance easier

### Implementation Details

The error response standardization includes:

1. **ErrorResponse Class** (`app/Http/Responses/ErrorResponse.php`) - Centralized error response creation
2. **ApiResponseHelper Class** (`app/Helpers/ApiResponseHelper.php`) - Helper functions for error responses
3. **Updated Controllers** - All API controllers now use standardized error responses
4. **Preserved Success Responses** - All existing success response formats remain unchanged

For detailed information about the error response format and examples, see the [Error Handling](#error-handling) section below.

## Authentication

-   Scheme: JWT Bearer
-   Header: Authorization: Bearer <access_token>
-   Token issuance: via User Account login

Refresh token behavior is implementation-specific; endpoints below return access and refresh tokens where applicable.

## Conventions

-   Pagination
    -   Query params: page (default 1), per_page (default 10; caps may vary by endpoint)
    -   Response meta: { current_page, per_page, total }
-   Filtering and sorting are endpoint-specific and documented per route.
-   Monetary amounts are in the smallest currency unit where noted (e.g., kobo for NGN) or as decimal amounts when returned in records.

# User Account API

Public endpoints do not require authentication. Authenticated endpoints require a valid JWT access token.

## Public Endpoints

### POST /api/auth/register

Register a new user account.

Request body:

```json path=null start=null
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "password": "SecurePassword123!",
    "phone": "123-456-7890"
}
```

Response:

```json path=null start=null
{
    "status": true,
    "message": "User Created Successfully"
}
```

### POST /api/auth/login

Authenticate user and return tokens.

Request body:

```json path=null start=null
{
    "email": "john.doe@example.com",
    "password": "SecurePassword123!"
}
```

Success Response:

```json path=null start=null
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "group_id": 2
}
```

Error Response Examples:

```json path=null start=null
{
    "success": false,
    "code": "INVALID_CREDENTIALS",
    "message": "Unauthorized",
    "errors": null,
    "data": null
}
```

### POST /api/auth/forgot-password

Send a password reset email/code.

Request body:

```json path=null start=null
{ "email": "john.doe@example.com" }
```

Response:

```json path=null start=null
{
    "success": true,
    "message": "Please check your email for a 6 digit pin"
}
```

### POST /api/auth/reset-password

Reset password using a verification token.

Request body:

```json path=null start=null
{
    "email": "john.doe@example.com",
    "token": "123456",
    "password": "NewSecurePassword123!",
    "password_confirmation": "NewSecurePassword123!"
}
```

Response:

```json path=null start=null
{
    "success": true,
    "message": "Your password has been reset"
}
```

## Authenticated Endpoints

### POST /api/auth/logout

Invalidate the current JWT access token.

Response:

```json path=null start=null
{ "message": "Successfully logged out" }
```

### POST /api/auth/signout

Alternative logout endpoint (also invalidates the current token).

Response:

```json path=null start=null
{ "message": "Successfully logged out" }
```

### GET /api/user/profile

Retrieve the authenticated user's profile.

Response:

```json path=null start=null
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

Check the current user's role/status.

Response:

```json path=null start=null
{ "status": "user", "message": "User is a standard user" }
```

# Subscription API

Endpoints for payment processing and subscription status. Uses Paystack for payment initiation and callbacks.

## Payment Processing

### POST /api/pay

Initiate a subscription payment.

Request body:

```json path=null start=null
{
    "planType": "23",
    "amount": 500000,
    "callBackUrl": "https://your-app.com/payment-callback"
}
```

Success Response:

```json path=null start=null
{ "authorization_url": "https://checkout.paystack.com/0peioxfhpn" }
```

Error Response Example:

```json path=null start=null
{
    "success": false,
    "code": "PAYMENT_INITIATION_ERROR",
    "message": "Failed to initiate payment. Please try again.",
    "errors": ["Detailed error message for debugging"],
    "data": null
}
```

### GET /api/pay/callback

Payment processor callback (handled by the server). Not typically called directly by clients.

### GET /api/pay/reference/{reference}

Check payment status by reference.

Response:

```json path=null start=null
{
    "exists": true,
    "amount": 5000.0,
    "reference": "7PVGX8MEK85E",
    "planName": "Premium Monthly",
    "planType": "premium",
    "status": "Successful",
    "message": "Completed",
    "active": "active"
}
```

## Subscription Management

### GET /api/pay/history

Get the authenticated user's payment history.

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 1,
            "amount": 5000.0,
            "status": "active",
            "reference": "7PVGX8MEK85E",
            "created_at": "2025-07-01T10:00:00Z",
            "plan": { "name": "Premium Monthly", "duration": "30 days" }
        }
    ],
    "meta": { "current_page": 1, "per_page": 10, "total": 5 }
}
```

### GET /api/pay/status

Check if the user has an active subscription.

Response:

```json path=null start=null
{ "status": "active", "message": "User has an active subscription" }
```

# Content API

Content browsing, retrieval, and search.

## Public Endpoints

### GET /api/articles/sample

Retrieve sample articles for unsubscribed users.

Query parameters:

-   m (string, optional): text | audio | video | bytes (default text)
-   n (integer, optional): news type ID

Response:

```json path=null start=null
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

Example:

```bash path=null start=null
curl -X GET "https://api.example.com/api/articles/sample?m=video&n=5"
```

### GET /api/plans

List available subscription plans.

Response:

```json path=null start=null
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

Requires a valid JWT and an active subscription.

### GET /api/articles

List articles with filters and pagination.

Query parameters:

-   s (string, optional): search term
-   m (string, optional): text | audio | video | bytes
-   n (integer, optional): news type ID
-   page, per_page (optional)
-   sort_by (title | news_date | created_at, default news_date)
-   sort_order (asc | desc, default desc)

Response:

```json path=null start=null
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
    "meta": { "current_page": 1, "per_page": 10, "total": 100 }
}
```

### GET /api/articles/{slug}

Retrieve a single article by slug.

Response:

```json path=null start=null
{
    "id": 1,
    "title": "Detailed Article Title",
    "body": "This is the full content of the article...",
    "mediaType": "text",
    "featuredImage": "https://example.com/storage/featured_image/article_image.jpg",
    "comments_count": 5,
    "comments": [
        {
            "id": 1,
            "body": "This is the first approved comment.",
            "user": { "name": "John Doe" }
        },
        {
            "id": 2,
            "body": "Another insightful comment.",
            "user": { "name": "Jane Smith" }
        }
    ]
}
```

### GET /api/articles/search

Search articles by title, content, or tags.

Query parameters:

-   q (string, required)
-   limit (integer, optional; default 10)

Success Response:

```json path=null start=null
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

Error Response Example:

```json path=null start=null
{
    "success": false,
    "code": "SEARCH_QUERY_REQUIRED",
    "message": "Search query is required",
    "errors": null,
    "data": null
}
```

# Investment API

Stock investment recommendations (stock picks).

## Subscriber Endpoints

Requires a valid JWT and an active subscription.

### GET /api/stockpicks

List all stock picks with associated newsletter details.

Response:

```json path=null start=null
[
    {
        "id": 1,
        "symbol": "AAPL",
        "newsletter_id": 42,
        "recommendation_date": "2025-06-15",
        "initial_price": 185.24,
        "current_price": 210.5,
        "newsletter": {
            "id": 42,
            "slug": "apple-stock-analysis",
            "mediaType": "text"
        }
    }
]
```

# Community API

Endpoints for managing article comments (threaded), available to subscribed users. Comments include the article slug in the newsletter object. Admin/moderator endpoints are under Admin API below.

## Subscriber Endpoints

### GET /api/comments

List comments for a newsletter/article with filtering and sorting.

Query parameters:

-   newsletter_id (integer, required)
-   status (optional): pending | approved | rejected | spam
-   per_page (optional; default 10)
-   sort (optional; newest | oldest; default newest)

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 1,
            "content": "Great article! Very informative.",
            "status": "approved",
            "created_at": "2025-07-01T10:00:00Z",
            "user": { "id": 1, "name": "John Doe" },
            "author_name": null,
            "replies": [
                {
                    "id": 2,
                    "content": "I agree! Thanks for sharing.",
                    "status": "approved",
                    "created_at": "2025-07-01T11:00:00Z",
                    "user": { "id": 2, "name": "Jane Smith" },
                    "author_name": null,
                    "replies": []
                }
            ]
        }
    ],
    "meta": { "total": 15 }
}
```

### POST /api/comments

Create a new comment or reply. Authenticated users or guests may post; guest fields required if no user_id.

Request body:

```json path=null start=null
{
    "newsletter_id": 42,
    "content": "This is a very insightful comment about the article.",
    "parent_id": 1,
    "author_name": "Guest Commenter",
    "author_email": "guest@example.com"
}
```

Success Response:

```json path=null start=null
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

Error Response Example:

```json path=null start=null
{
    "success": false,
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "errors": {
        "newsletter_id": ["The newsletter id field is required."],
        "content": ["The content field is required."]
    },
    "data": null
}
```

### GET /api/comments/{comment}

Get single comment details, including author and replies.

Response:

```json path=null start=null
{
    "data": {
        "id": 1,
        "content": "Great article! Very informative.",
        "status": "approved",
        "created_at": "2025-07-01T10:00:00Z",
        "user": { "id": 1, "name": "John Doe" },
        "author_name": null,
        "replies": []
    }
}
```

### PUT /api/comments/{comment}

Update a comment. Editing typically resets moderation status to pending.

Request body:

```json path=null start=null
{ "content": "This is my updated and improved comment." }
```

Response:

```json path=null start=null
{
    "message": "Comment updated successfully",
    "data": {
        "id": 1,
        "content": "This is my updated and improved comment.",
        "status": "pending",
        "created_at": "2025-07-01T10:00:00Z",
        "user": { "id": 1, "name": "John Doe" },
        "author_name": null,
        "replies": []
    }
}
```

### DELETE /api/comments/{comment}

Delete a comment.

Response:

```json path=null start=null
{ "message": "Comment deleted successfully" }
```

# Admin API

All endpoints require Authorization: Bearer <token> and admin privileges.

## User Management

### GET /api/admin/users

List users with pagination and optional filters.

Query parameters:

-   role (optional): admin | user
-   subscriber_status (optional): active | never_subscribed | expired_non_renewed
-   per_page (optional; default 10)

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 1,
            "name": "adminuser1",
            "email": "admin@example.com",
            "role_id": 1,
            "status": "approved",
            "created_at": "2025-06-15T08:30:00Z",
            "subscriber_status": "active",
            "last_payment_date": "2025-06-15T08:30:00Z"
        }
    ],
    "meta": { "current_page": 1, "per_page": 10, "total": 50 }
}
```

### POST /api/admin/users/create

Create a new user.

Request body:

```json path=null start=null
{
    "first_name": "Admin",
    "last_name": "User",
    "email": "admin2@example.com",
    "password": "AdminSecurePassword123!",
    "phone": "1234567890",
    "role_id": 1
}
```

Response:

```json path=null start=null
{ "status": true, "message": "User Created Successfully" }
```

## Article Management

### POST /api/admin/articles/add

Create a new article.

Request body:

```json path=null start=null
{
    "mediaType": "text",
    "news_type_id": "1",
    "name": "Internal Article Name",
    "title": "New Article Title",
    "news_date": "2025-07-01",
    "body": "This is the content of the new article. It should be detailed and informative.",
    "featuredImage": "https://example.com/storage/featured_image/new_article_image.jpg",
    "featured": false,
    "status": "draft",
    "author_id": 1
}
```

Response:

```json path=null start=null
{ "status": "success", "data": { "id": 42, "title": "New Article Title" } }
```

### PUT /api/admin/articles/update/{slug}

Update an existing article by slug.

Request body (partial updates):

```json path=null start=null
{
    "title": "Updated Article Title",
    "body": "This is the updated content of the article.",
    "featured": true,
    "status": "published"
}
```

Response:

```json path=null start=null
{
    "message": "Article updated successfully!",
    "data": { "id": 42, "title": "Updated Article Title" }
}
```

## Media Management

### GET /api/admin/media

List uploaded media files.

Response:

```json path=null start=null
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

Upload a media file.

Request (multipart/form-data): file (required); folder (optional; default all)

Response:

```json path=null start=null
{
    "status": "success",
    "data": { "directory": "featured_image", "filename": "image_20250701.jpg" }
}
```

### GET /api/admin/articles/update-image-paths

Utility endpoint to fix featuredImage paths.

Response:

```json path=null start=null
"Image paths updated successfully!"
```

## Payment Management

### GET /api/admin/pay/all

List all payments with user details.

Query parameters:

-   per_page (optional; default 15; min 1; max 100)
-   all (boolean, optional; if true, returns all records without pagination)

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 1,
            "amount": 5000.0,
            "user": { "name": "John Doe", "email": "john.doe@example.com" },
            "status": "completed",
            "created_at": "2025-07-01T10:00:00Z"
        }
    ],
    "meta": { "current_page": 1, "per_page": 15, "total": 100 }
}
```

### GET /api/admin/pay/user/{identifier}

Get payment history for a user by ID or email.

Query parameters:

-   per_page (optional; default 15)

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 1,
            "amount": 5000.0,
            "user": {
                "id": 123,
                "first_name": "John",
                "last_name": "Doe",
                "email": "john.doe@example.com"
            },
            "status": "completed",
            "created_at": "2025-07-01T10:00:00Z"
        }
    ],
    "meta": { "current_page": 1, "per_page": 15, "total": 5 }
}
```

### GET /api/admin/pay/summary

Summarize payments over standard time windows (this month, last month, this year, last year) with optional filters. Returns totals (sum of amounts), counts, and month-over-month/year-over-year deltas.

Query parameters:

-   user_id (integer, optional): Limit the summary to a specific user.
-   only_success (boolean, optional; default true): If true, filters to successful payments, defined as any of:
    -   status = "active"
    -   status_response = "success"
    -   gateway_response = "successful"
-   date_field (string, optional; default created_at): Which timestamp to bucket by. Allowed values: created_at | updated_at | due_date

Response:

```json path=null start=null
{
    "period": {
        "this_month": {
            "start": "2025-09-01 00:00:00",
            "end": "2025-09-30 23:59:59"
        },
        "last_month": {
            "start": "2025-08-01 00:00:00",
            "end": "2025-08-31 23:59:59"
        },
        "this_year": {
            "start": "2025-01-01 00:00:00",
            "end": "2025-12-31 23:59:59"
        },
        "last_year": {
            "start": "2024-01-01 00:00:00",
            "end": "2024-12-31 23:59:59"
        }
    },
    "filters": {
        "user_id": 123,
        "only_success": true,
        "date_field": "created_at"
    },
    "totals": {
        "this_month": 150000.0,
        "last_month": 120000.0,
        "month_change_abs": 30000.0,
        "month_change_pct": 25.0,
        "this_year": 1250000.0,
        "last_year": 950000.0,
        "year_change_abs": 300000.0,
        "year_change_pct": 31.58,
        "subscription_total": 3200000.0
    },
    "counts": {
        "this_month": 45,
        "last_month": 36,
        "this_year": 410,
        "last_year": 355,
        "subscription_count": 980
    }
}
```

### POST /api/admin/pay/add

Manually add a payment record.

Request body:

```json path=null start=null
{
    "user_id": 123,
    "amount": 5000.0,
    "reference": "MANUAL_PAYMENT_001",
    "status": "success",
    "payment_method": "bank transfer",
    "transaction_id": "TXN_001",
    "plan_id": 1,
    "due_date": "2026-09-15",
    "paid_at": "2025-09-15T10:30:00Z",
    "currency": "NGN"
}
```

Response:

```json path=null start=null
{
    "status": "success",
    "message": "Payment record added successfully",
    "data": {
        "id": 456,
        "user_id": 123,
        "amount": 5000.0,
        "reference": "MANUAL_PAYMENT_001",
        "status": "success",
        "payment_method": "bank transfer",
        "transaction_id": "TXN_001",
        "plan_id": 1,
        "due_date": "2026-09-15",
        "paid_at": "2025-09-15T10:30:00Z",
        "currency": "NGN",
        "created_at": "2025-09-15T10:30:00Z",
        "updated_at": "2025-09-15T10:30:00Z",
        "user": {
            "id": 123,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com"
        }
    }
}
```

## Comment Moderation

### GET /api/admin/comments

List all comments with comprehensive filtering capabilities.

Query parameters:

-   `status` (optional): Filter by status - `pending`, `approved`, `rejected`, `spam`, or `all`
-   `newsletter_id` (optional): Filter by specific newsletter ID
-   `author_type` (optional): Filter by author type - `guest` or `registered`
-   `date_from` (optional): Filter comments created after this date (YYYY-MM-DD)
-   `date_to` (optional): Filter comments created before this date (YYYY-MM-DD)
-   `user_id` (optional): Filter by specific user ID
-   `ip_address` (optional): Filter by IP address
-   `search` (optional): Search across content, author name, and email
-   `per_page` (optional): Number of results per page (default: 20, max: 100)
-   `sort` (optional): Sort order - `newest` or `oldest` (default: newest)

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 1,
            "content": "This is a comment awaiting moderation.",
            "status": "pending",
            "author": {
                "id": 123,
                "name": "John Doe",
                "email": "john@example.com",
                "avatar": null
            },
            "newsletter": {
                "id": 42,
                "title": "Market Analysis Report",
                "slug": "market-analysis-report"
            },
            "parent_id": null,
            "replies_count": 0,
            "replies": [],
            "created_at": "2025-07-01T10:00:00Z",
            "updated_at": "2025-07-01T10:00:00Z",
            "moderated_at": null,
            "moderated_by": null,
            "can_edit": true,
            "can_delete": true
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 20,
        "total": 95
    }
}
```

### GET /api/admin/comments/pending

List comments awaiting moderation with enhanced filtering.

Query parameters:

-   `newsletter_id` (optional): Filter by specific newsletter ID
-   `author_type` (optional): Filter by author type - `guest` or `registered`
-   `date_from` (optional): Filter comments created after this date (YYYY-MM-DD)
-   `date_to` (optional): Filter comments created before this date (YYYY-MM-DD)
-   `search` (optional): Search across content, author name, and email
-   `per_page` (optional): Number of results per page (default: 20, max: 100)
-   `sort` (optional): Sort order - `newest` or `oldest` (default: newest)

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 1,
            "content": "Pending comment awaiting review.",
            "status": "pending",
            "author": {
                "id": null,
                "name": "Guest User",
                "email": "guest@example.com",
                "avatar": null
            },
            "newsletter": {
                "id": 42,
                "title": "Market Analysis Report",
                "slug": "market-analysis-report"
            },
            "created_at": "2025-07-01T10:00:00Z",
            "updated_at": "2025-07-01T10:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 20,
        "total": 45
    }
}
```

### GET /api/admin/comments/flagged

List potentially problematic comments based on content analysis.

Query parameters:

-   `newsletter_id` (optional): Filter by specific newsletter ID
-   `date_from` (optional): Filter comments created after this date (YYYY-MM-DD)
-   `date_to` (optional): Filter comments created before this date (YYYY-MM-DD)
-   `flag_type` (optional): Filter by flag type - `spam`, `inappropriate`, `reported`, or `all` (default: all)
-   `per_page` (optional): Number of results per page (default: 20, max: 100)
-   `sort` (optional): Sort order - `newest` or `oldest` (default: newest)

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 2,
            "content": "This looks like spam content with casino links.",
            "status": "pending",
            "author": {
                "id": 456,
                "name": "Spam User",
                "email": "spam@example.com",
                "avatar": null
            },
            "newsletter": {
                "id": 42,
                "title": "Market Analysis Report",
                "slug": "market-analysis-report"
            },
            "created_at": "2025-07-01T11:00:00Z",
            "updated_at": "2025-07-01T11:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 2,
        "per_page": 20,
        "total": 25
    }
}
```

### GET /api/admin/comments/search

Advanced search across all comment fields.

Query parameters:

-   `q` (required): Search term (min: 2 characters, max: 255)
-   `newsletter_id` (optional): Filter by specific newsletter ID
-   `status` (optional): Filter by status - `pending`, `approved`, `rejected`, `spam`, or `all`
-   `date_from` (optional): Filter comments created after this date (YYYY-MM-DD)
-   `date_to` (optional): Filter comments created before this date (YYYY-MM-DD)
-   `per_page` (optional): Number of results per page (default: 20, max: 100)
-   `sort` (optional): Sort order - `newest` or `oldest` (default: newest)

Response:

```json path=null start=null
{
    "data": [
        {
            "id": 3,
            "content": "This comment contains the search term.",
            "status": "approved",
            "author": {
                "id": 123,
                "name": "John Doe",
                "email": "john@example.com",
                "avatar": null
            },
            "newsletter": {
                "id": 45,
                "title": "Weekly Newsletter",
                "slug": "weekly-newsletter"
            },
            "created_at": "2025-07-01T12:00:00Z",
            "updated_at": "2025-07-01T12:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 20,
        "total": 1
    }
}
```

### GET /api/admin/comments/{comment}/history

Get moderation history for a specific comment.

Response:

```json path=null start=null
{
    "data": {
        "comment_id": 1,
        "content": "This comment was moderated.",
        "current_status": "approved",
        "moderation_history": [
            {
                "action": "created",
                "status": "pending",
                "created_at": "2025-07-01T10:00:00Z",
                "author": {
                    "name": "John Doe",
                    "email": "john@example.com",
                    "user_id": 123
                }
            },
            {
                "action": "moderated",
                "status": "approved",
                "moderated_by": {
                    "id": 1,
                    "name": "Admin User"
                },
                "moderated_at": "2025-07-01T14:30:00Z",
                "reason": "Comment meets community guidelines"
            }
        ]
    }
}
```

### PATCH /api/admin/comments/{comment}/moderate

Change a comment's moderation status.

Request body:

```json path=null start=null
{
    "status": "approved",
    "reason": "Comment meets community guidelines"
}
```

Response:

```json path=null start=null
{
    "success": true,
    "message": "Comment moderated successfully",
    "data": {
        "id": 1,
        "content": "This comment was moderated.",
        "status": "approved",
        "author": {
            "id": 123,
            "name": "John Doe",
            "email": "john@example.com",
            "avatar": null
        },
        "moderated_at": "2025-07-01T14:30:00Z",
        "moderated_by": {
            "id": 1,
            "name": "Admin User"
        }
    }
}
```

**Validation Rules:**
- `status`: Required, must be one of: `approved`, `rejected`, `spam`
- `reason`: Optional, string, max 500 characters

### PATCH /api/admin/comments/bulk-moderate

Bulk moderate multiple comments.

Request body:

```json path=null start=null
{
    "comment_ids": [1, 2, 3],
    "status": "approved",
    "reason": "Bulk approval of valid comments"
}
```

Response:

```json path=null start=null
{
    "success": true,
    "message": "Successfully moderated 3 comments",
    "updated_count": 3
}
```

**Error Response (Validation Error):**

```json path=null start=null
{
    "success": false,
    "code": "VALIDATION_ERROR",
    "message": "Validation failed. Please check your input and try again.",
    "errors": {
        "status": [
            "The status field is required."
        ],
        "comment_ids": [
            "The comment ids field is required."
        ]
    },
    "data": null
}
```

**Validation Rules:**
- `comment_ids`: Required, must be an array of valid comment IDs
- `status`: Required, must be one of: `approved`, `rejected`, `spam`
- `reason`: Optional, string, max 500 characters

### GET /api/admin/comments/stats

Get comment moderation statistics.

Response:

```json path=null start=null
{
    "data": {
        "pending": 15,
        "approved": 120,
        "rejected": 8,
        "spam": 25,
        "total": 168,
        "today": 5,
        "this_week": 32
    }
}
```

## Stock Pick Management

### POST /api/admin/stockpicks

Create a new stock pick recommendation.

Request body:

```json path=null start=null
{
    "symbol": "AAPL",
    "newsletter_id": 42,
    "recommendation_date": "2025-07-01",
    "initial_price": 185.24
}
```

### PUT /api/admin/stockpicks/{stockPick}

Update stock pick details (partial updates allowed).

```json path=null start=null
{
    "symbol": "MSFT",
    "newsletter_id": 45,
    "recommendation_date": "2025-07-01",
    "initial_price": 420.5
}
```

### PATCH /api/admin/stockpicks/{stockPick}/price

Update only the current price of a stock pick.

```json path=null start=null
{ "current_price": 430.25 }
```

### POST /api/admin/stockpicks/batch-update-prices

Batch update current prices for multiple stock picks.

Request body:

```json path=null start=null
{
    "updates": [
        { "id": 1, "current_price": 215.75 },
        { "id": 2, "current_price": 430.25 }
    ]
}
```

Response:

```json path=null start=null
[
    { "id": 1, "symbol": "AAPL", "current_price": 215.75 },
    { "id": 2, "symbol": "MSFT", "current_price": 430.25 }
]
```

# Admin Metrics API

Metrics endpoints for administrative reporting. Require admin privileges.

### GET /api/admin/metrics/user-funnel

Track user conversion from visitor to active user.

Query parameters:

-   start (date YYYY-MM-DD)
-   end (date YYYY-MM-DD)

Response:

```json path=null start=null
{ "visitors": 0, "registered": 150, "subscribed": 85, "active": 60 }
```

### GET /api/admin/metrics/subscription-health

Subscription KPIs.

Response:

```json path=null start=null
{ "mrr": 42500.0, "churn_rate": 5.2, "clv": 500.0, "expansion": 7500.0 }
```

### GET /api/admin/metrics/cohorts/{period}

Retention by signup period. Path parameter period: month | week

Response:

```json path=null start=null
{ "2025-08": [0.85, 0.7, 0.65], "2025-07": [0.9, 0.75, 0.68] }
```

### GET /api/admin/metrics/engagement/{user_id?}

Feature usage and activity metrics. Optional user_id for per-user metrics.

Response:

```json path=null start=null
{
    "feature_usage": [
        { "feature_name": "stock_picks", "total_usage": 420 },
        { "feature_name": "premium_content", "total_usage": 315 }
    ]
}
```

### GET /api/admin/metrics/payment-analytics

Payment analytics.

Response:

```json path=null start=null
{
    "total_revenue": 125000.0,
    "avg_payment": 5000.0,
    "success_rate": 92.5,
    "payment_methods": [
        { "payment_method": "card", "count": 85 },
        { "payment_method": "bank", "count": 15 }
    ]
}
```

# Status Codes

-   200 OK: Request successful.
-   201 Created: Resource successfully created.
-   400 Bad Request: Malformed or invalid request.
-   401 Unauthorized: Missing, invalid, or expired token.
-   403 Forbidden: Insufficient permissions.
-   404 Not Found: Resource not found.
-   422 Unprocessable Entity: Validation errors or semantic issues.
-   500 Internal Server Error: Unexpected server error.

# Error Handling

All error responses follow a standardized JSON structure for consistency across all endpoints.

## Standardized Error Response Format

```json path=null start=null
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

### Common Error Codes

| Error Code | Description | HTTP Status |
|-------------|-------------|-------------|
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

### Error Response Examples

#### Validation Error
```json path=null start=null
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

#### Not Found Error
```json path=null start=null
{
    "success": false,
    "code": "NOT_FOUND",
    "message": "Resource not found",
    "errors": null,
    "data": null
}
```

#### Unauthorized Error
```json path=null start=null
{
    "success": false,
    "code": "UNAUTHORIZED",
    "message": "Invalid credentials",
    "errors": null,
    "data": null
}
```

#### Server Error
```json path=null start=null
{
    "success": false,
    "code": "SERVER_ERROR",
    "message": "Internal server error",
    "errors": ["Detailed error message for debugging"],
    "data": null
}
```

```json path=null start=null
{
    "status": false,
    "message": "Payment failed",
    "error": "Plan not found"
}
```
