# Search API Documentation

## Table of Contents
- [Overview](#overview)
- [Authenticated Endpoints](#authenticated-endpoints)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
The Search API allows authenticated users to search newsletters by:
- Title content
- Body content
- Tags

Requires:
- JWT authentication (`auth.jwt` middleware)
- Active subscription (`subscribed` middleware)

## Authenticated Endpoints

### Search Newsletters
`GET /articles/search`

Search newsletters by keyword in title, body or tags.

#### Parameters:
```json
{
  "q": "required|string|min:1", // search query
  "limit": "optional|integer|min:1|max:100" // max results to return (default: 10)
}
```

#### Example Request:
```bash
curl -X GET \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  "https://api.example.com/articles/search?q=finance"
```

#### Successful Response (200):
Returns matching newsletters or empty array if no matches found.

With results:
```json
[
  {
    "id": 42,
    "slug": "finance-market-update-q3-2025",
    "title": "Finance Market Update Q3 2025",
    "mediaType": "text"
  }
]
```

No results:
```json
[]
```

#### Empty Query Response (400):
```json
{
  "error": "Search query is required"
}
```

## Status Codes
- 200: OK - Successful search with results
- 400: Bad Request - Missing or invalid parameters
- 401: Unauthorized - Missing or invalid authentication
- 403: Forbidden - Valid authentication but no active subscription

## Error Handling
Errors return a JSON response with details:

```json
{
  "error": "Search query is required"
}
