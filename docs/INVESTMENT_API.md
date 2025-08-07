# Investment Tools API Documentation

## Table of Contents
- [Overview](#overview)
- [Viewing Stock Picks](#viewing-stock-picks)
- [Managing Stock Picks](#managing-stock-picks)
- [Batch Operations](#batch-operations)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
Endpoints for investment-related features including:
- Viewing recommended stock picks
- Managing stock pick recommendations (admin only)
- Batch updating stock prices (admin only)

## Viewing Stock Picks

### GET /api/stockpicks
List all stock picks

#### Success Response:
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
      "performance_percent": 13.64
    }
  ]
}
```

## Managing Stock Picks

### POST /api/stockpicks
Create stock pick (admin only)

### PUT /api/stockpicks/{stockPick}
Update stock pick (admin only)

## Batch Operations

### PATCH /api/stockpicks/{stockPick}/price
Update single stock price (admin only)

### POST /api/stockpicks/batch-update-prices
Batch update stock prices (admin only)

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
  "message": "Stock pick not found"
}
