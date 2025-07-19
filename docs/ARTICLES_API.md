# Articles API Documentation

## Table of Contents
- [Overview](#overview)
- [Authenticated Endpoints](#authenticated-endpoints)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
The Articles API allows authenticated users to:
- List articles with pagination
- Sort articles by different fields
- Get articles in the same format as search results

Requires:
- JWT authentication (`auth.jwt` middleware)
- Active subscription (`subscribed` middleware)

## Authenticated Endpoints

### List Articles
`GET /articles/list`

Get paginated list of articles with sorting.

#### Parameters:
```json
{
  "page": "optional|integer|min:1",
  "per_page": "optional|integer|min:1|max:100",
  "sort_by": "optional|in:title,news_date,created_at",
  "sort_order": "optional|in:asc,desc"
}
```

#### Example Request:
```bash
curl -X GET \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  "https://api.example.com/articles/list?page=2&per_page=5&sort_by=title&sort_order=asc"
```

#### Successful Response (200):
```json
[
  {
    "id": 42,
    "slug": "finance-market-update-q3-2025",
    "title": "Finance Market Update Q3 2025",
    "mediaType": "text"
  },
  {
    "id": 56,
    "slug": "investment-tips-2025",
    "title": "Investment Tips for 2025",
    "mediaType": "audio"
  }
]
```

## Status Codes
- 200: OK - Successful request with results
- 400: Bad Request - Invalid parameters
- 401: Unauthorized - Missing or invalid authentication
- 403: Forbidden - Valid authentication but no active subscription

## Error Handling
Errors return a JSON response with details:

```json
{
  "error": "Invalid sort_by parameter"
}
