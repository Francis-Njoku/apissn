# Subscription API Documentation

## Table of Contents
- [Overview](#overview)
- [Subscription Plans](#subscription-plans)
- [Payment Processing](#payment-processing)
- [Payment History](#payment-history)
- [Subscription Status](#subscription-status)
- [Status Codes](#status-codes)
- [Error Handling](#error-handling)

## Overview
Endpoints for managing subscriptions including:
- Viewing available plans
- Processing payments
- Checking payment history
- Verifying subscription status

## Subscription Plans

### GET /api/plans
List available subscription plans

#### Success Response:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Monthly",
      "price": 5000,
      "duration_days": 30
    },
    {
      "id": 2,
      "name": "Annual",
      "price": 50000,
      "duration_days": 365
    }
  ]
}
```

## Payment Processing

### POST /api/pay
Initiate subscription payment

#### Request:
```json
{
  "plan_id": 1,
  "email": "user@example.com",
  "amount": 5000
}
```

### GET /api/pay/callback
Payment processor callback

## Payment History

### GET /api/pay/history
View user's payment history

### GET /api/pay/reference/{reference}
Check payment by reference

## Subscription Status

### GET /api/user/role
Check subscription status (included in role response)

## Status Codes
- 200: OK
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found

## Error Handling
```json
{
  "status": false,
  "message": "Payment failed",
  "error": "Insufficient funds"
}
