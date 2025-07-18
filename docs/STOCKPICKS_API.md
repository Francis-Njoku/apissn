# Stock Picks API Documentation

## Table of Contents
- [Overview](#overview)
- [Public Endpoints](#public-endpoints)
- [Authenticated Endpoints](#authenticated-endpoints)
- [Admin Endpoints](#admin-endpoints)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
The Stock Picks API allows tracking of recommended stocks with:
- Ticker symbol
- Recommendation date
- Initial price at recommendation
- Current price (updated daily)

Admin users can:
- Create new stock picks
- Update stock pick details
- Update current prices (single or batch)

## Public Endpoints

### Get All Stock Picks
`GET /api/stockpicks`

Returns all stock picks with their current performance data.

#### Example Response:
```json
{
  "data": [
    {
      "id": 1,
      "symbol": "AAPL",
      "newsletter_id": 42,
      "recommendation_date": "2025-06-15",
      "initial_price": 185.24,
      "current_price": 210.50,
      "performance_percent": 13.64,
      "newsletter": {
        "id": 42,
        "slug": "tech-stocks-2025",
        "mediaType":"text"
      }
    }
  ]
}
```

## Authenticated Endpoints

None - All write operations require admin privileges.

## Admin Endpoints

### Create Stock Pick
`POST /api/stockpicks`

Create a new stock pick recommendation. Requires admin role.

#### Request:
```json
{
  "symbol": "MSFT",
  "newsletter_id": 42,
  "recommendation_date": "2025-06-20",
  "initial_price": 420.50,
  "current_price": null // Optional on creation
}
```

#### Success Response (201):
```json
{
  "message": "Stock pick created successfully",
  "data": {
    "id": 2,
    "symbol": "MSFT",
    "newsletter_id": 42,
    "recommendation_date": "2025-06-20",
    "initial_price": 420.50,
    "current_price": null
  }
}
```

### Update Stock Pick
`PUT /api/stockpicks/{id}`

Update stock pick details. Requires admin role.

#### Request:
```json
{
  "symbol": "MSFT",
  "initial_price": 425.00
}
```

### Update Single Price
`PATCH /api/stockpicks/{id}/price`

Update just the current price of a stock pick. Requires admin role.

#### Request:
```json
{
  "current_price": 450.75
}
```

### Batch Update Prices
`POST /api/stockpicks/batch-update-prices`

Update multiple stock prices at once. Requires admin role.

#### Request:
```json
{
  "updates": [
    {
      "id": 1,
      "current_price": 215.25
    },
    {
      "id": 2,
      "current_price": 455.00
    }
  ]
}
```

#### Success Response:
```json
{
  "message": "Prices updated successfully",
  "data": [
    {
      "id": 1,
      "symbol": "AAPL",
      "current_price": 215.25
    },
    {
      "id": 2,
      "symbol": "MSFT",
      "current_price": 455.00
    }
  ]
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
    "symbol": ["The symbol field is required."],
    "initial_price": ["The initial price must be a number."]
  }
}
