# Investment Tools API Documentation

## Table of Contents

-   [Overview](#overview)
-   [Public Endpoints](#public-endpoints)
-   [Admin Endpoints](#admin-endpoints)
-   [Status Codes](#status-codes)
-   [Error Handling](#error-handling)

## Overview

Endpoints for managing stock investment recommendations and tracking performance. Admin endpoints require admin privileges. This section details how users can view stock picks and how administrators can manage them, including price updates.

## Public Endpoints

### GET /api/stockpicks

List all stock picks with associated newsletter details. This endpoint retrieves all stock recommendations, including basic information about the related newsletter.

#### Response:

Returns a JSON array of stock pick objects, each containing:

-   `id` (integer): Unique identifier for the stock pick.
-   `symbol` (string): The stock ticker symbol (e.g., "AAPL").
-   `newsletter_id` (integer, nullable): The ID of the newsletter this pick is associated with.
-   `recommendation_date` (string): The date the stock pick was recommended (YYYY-MM-DD).
-   `initial_price` (float): The price of the stock at the time of recommendation.
-   `current_price` (float, nullable): The current market price of the stock.
-   `newsletter` (object, nullable): Basic details of the associated newsletter:
    -   `id` (integer): Unique identifier for the newsletter.
    -   `slug` (string): URL-friendly identifier for the newsletter.
    -   `mediaType` (string): The media type of the newsletter (e.g., 'text').

```json
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

## Admin Endpoints

Requires admin privileges.

### POST /api/stockpicks

Create new stock pick recommendation. Allows administrators to add new stock recommendations to the system.

#### Request Body:

-   `symbol` (string, required): The stock ticker symbol. Max 10 characters.
-   `newsletter_id` (integer, optional): The ID of the associated newsletter. Must exist in the `newsletter` table if provided.
-   `recommendation_date` (string, required): The date of recommendation in 'YYYY-MM-DD' format.
-   `initial_price` (float, required): The stock price at the time of recommendation.
-   `current_price` (float, optional): The current market price of the stock.

```json
{
    "symbol": "MSFT",
    "newsletter_id": 45,
    "recommendation_date": "2025-07-01",
    "initial_price": 420.5
}
```

#### Response:

Returns the newly created stock pick details, including associated newsletter information.

-   `id` (integer): Unique identifier for the new stock pick.
-   `symbol` (string): The stock ticker symbol.
-   `newsletter_id` (integer, nullable): The ID of the associated newsletter.
-   `recommendation_date` (string): The date of recommendation.
-   `initial_price` (float): The initial stock price.
-   `current_price` (float, nullable): The current stock price.
-   `newsletter` (object, nullable): Basic details of the associated newsletter.

```json
{
    "id": 2,
    "symbol": "MSFT",
    "newsletter_id": 45,
    "recommendation_date": "2025-07-01",
    "initial_price": 420.5,
    "current_price": null,
    "newsletter": {
        "id": 45,
        "slug": "microsoft-analysis",
        "mediaType": "text"
    }
}
```

### PUT /api/stockpicks/{stockPick}

Update stock pick details. Allows administrators to modify existing stock pick records.

#### Parameters:

-   `stockPick` (integer, required): The ID of the stock pick to update.

#### Request Body:

Fields to be updated. All fields are optional; provide only those that need modification.

-   `symbol` (string, optional): New stock ticker symbol. Max 10 characters.
-   `newsletter_id` (integer, optional): New ID of the associated newsletter.
-   `recommendation_date` (string, optional): New recommendation date ('YYYY-MM-DD').
-   `initial_price` (float, optional): New initial stock price.
-   `current_price` (float, optional): New current stock price.

```json
{
    "symbol": "MSFT",
    "newsletter_id": 45,
    "recommendation_date": "2025-07-01",
    "initial_price": 420.5
}
```

#### Response:

Returns the updated stock pick details, including associated newsletter information.

```json
{
    "id": 2,
    "symbol": "MSFT",
    "newsletter_id": 45,
    "recommendation_date": "2025-07-01",
    "initial_price": 420.5,
    "current_price": 430.25,
    "newsletter": {
        "id": 45,
        "slug": "microsoft-analysis",
        "mediaType": "text"
    }
}
```

### PATCH /api/stockpicks/{stockPick}/price

Update stock price only. Allows administrators to quickly update the current price of a stock pick.

#### Parameters:

-   `stockPick` (integer, required): The ID of the stock pick to update.

#### Request Body:

-   `current_price` (float, required): The new current market price of the stock.

```json
{
    "current_price": 430.25
}
```

#### Response:

Returns the updated stock pick with the new current price.

```json
{
    "id": 2,
    "symbol": "MSFT",
    "current_price": 430.25
}
```

### POST /api/stockpicks/batch-update-prices

Batch update multiple stock prices. Allows administrators to update the current prices for multiple stock picks in a single request.

#### Request Body:

-   `updates` (array, required): An array of objects, where each object contains the `id` of the stock pick and its new `current_price`.
    -   `id` (integer, required): The ID of the stock pick to update. Must exist in the `stock_picks` table.
    -   `current_price` (float, required): The new current market price for the stock.

```json
{
    "updates": [
        {
            "id": 1,
            "current_price": 215.75
        },
        {
            "id": 2,
            "current_price": 430.25
        }
    ]
}
```

#### Response:

Returns an array of the updated stock pick objects, each including their `id`, `symbol`, and new `current_price`.

```json
[
    {
        "id": 1,
        "symbol": "AAPL",
        "current_price": 215.75
    },
    {
        "id": 2,
        "symbol": "MSFT",
        "current_price": 430.25
    }
]
```

## Status Codes

-   `200 OK`: Request successful.
-   `201 Created`: Stock pick successfully created.
-   `400 Bad Request`: The request was malformed or invalid (e.g., missing required fields, invalid data format).
-   `401 Unauthorized`: Authentication failed. The provided token is missing, invalid, or expired.
-   `403 Forbidden`: The authenticated user does not have the necessary permissions (e.g., not an admin).
-   `404 Not Found`: The requested stock pick could not be found.
-   `422 Unprocessable Entity`: The request was valid but contained semantic errors, typically validation failures.

## Error Handling

Provides details about validation errors or other issues encountered during request processing.

```json
{
    "message": "Validation failed",
    "errors": {
        "symbol": ["The symbol field is required."],
        "initial_price": ["The initial price must be a number."]
    }
}
```
