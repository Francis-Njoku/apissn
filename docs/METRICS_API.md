# Admin Metrics API Documentation

## Table of Contents

-   [Overview](#overview)
-   [User Acquisition Funnel](#user-acquisition-funnel)
-   [Subscription Health](#subscription-health)
-   [Cohort Analysis](#cohort-analysis)
-   [Engagement Metrics](#engagement-metrics)
-   [Payment Analytics](#payment-analytics)
-   [Status Codes](#status-codes)
-   [Error Handling](#error-handling)

## Overview

Endpoints for administrative metrics reporting. Requires JWT authentication and admin privileges. All endpoints return JSON responses.

## User Acquisition Funnel

### GET /admin/metrics/user-funnel

Track user conversion from visitor to active user.

#### Query Parameters:

-   `start` (date): Start date (YYYY-MM-DD)
-   `end` (date): End date (YYYY-MM-DD)

#### Response:

```json
{
    "visitors": 0,
    "registered": 150,
    "subscribed": 85,
    "active": 60
}
```

## Subscription Health

### GET /admin/metrics/subscription-health

Key subscription performance indicators.

#### Response:

```json
{
    "mrr": 42500.0,
    "churn_rate": 5.2,
    "clv": 500.0,
    "expansion": 7500.0
}
```

## Cohort Analysis

### GET /admin/metrics/cohorts/{period}

Retention analysis by signup period.

#### Path Parameters:

-   `period` (string): Cohort period (month/week)

#### Response:

```json
{
    "2025-08": [0.85, 0.7, 0.65],
    "2025-07": [0.9, 0.75, 0.68]
}
```

## Engagement Metrics

### GET /admin/metrics/engagement/{user_id?}

Feature usage and activity metrics.

#### Path Parameters:

-   `user_id` (integer, optional): Specific user ID

#### Response:

```json
{
    "feature_usage": [
        { "feature_name": "stock_picks", "total_usage": 420 },
        { "feature_name": "premium_content", "total_usage": 315 }
    ]
}
```

## Payment Analytics

### GET /admin/metrics/payment-analytics

Payment performance metrics.

#### Response:

```json
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

## Status Codes

-   `200 OK`: Successful request
-   `401 Unauthorized`: Missing/invalid token
-   `403 Forbidden`: Non-admin user
-   `422 Unprocessable Entity`: Invalid parameters

## Error Handling

```json
{
    "error": "Invalid date format",
    "message": "Use YYYY-MM-DD format for dates"
}
```
