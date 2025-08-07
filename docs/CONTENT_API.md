# Content API Documentation

## Table of Contents
- [Overview](#overview)
- [Content Browsing](#content-browsing)
- [Article Details](#article-details)
- [Content Search](#content-search)
- [Content Filtering](#content-filtering)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
Endpoints for accessing and discovering content including:
- Article listings
- Individual article details
- Content search
- Media type filtering

## Content Browsing

### GET /api/articles
List all articles

#### Parameters:
- `page` - Page number
- `per_page` - Items per page (default: 10)
- `sort` - Sort order (newest, oldest)

### GET /api/articles/latest
Get latest articles

### GET /api/articles/by-media
Filter by media type

## Article Details

### GET /api/articles/{slug}
Get single article

#### Success Response:
```json
{
  "id": 42,
  "title": "Article Title",
  "content": "Article content...",
  "mediaType": "text",
  "publishedAt": "2025-07-01T10:00:00Z"
}
```

## Content Search

### GET /api/articles/search
Search content

#### Parameters:
- `q` - Search query (required)
- `limit` - Max results (default: 10)

#### Example:
```bash
curl -X GET \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  "https://api.example.com/articles/search?q=finance&limit=5"
```

## Content Filtering

### GET /api/articles/by-media
Filter by media type

#### Parameters:
- `type` - Media type (text, audio, video)

## Status Codes
- 200: OK
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found

## Error Handling
```json
{
  "message": "Article not found"
}
