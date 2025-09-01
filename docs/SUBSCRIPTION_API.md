# Subscription API Documentation

## Table of Contents

-   [Overview](#overview)
-   [Payment Processing](#payment-processing)
-   [Subscription Management](#subscription-management)
-   [Admin Endpoints](#admin-endpoints)
-   [Status Codes](#status-codes)
-   [Error Handling](#error-handling)

## Overview

Endpoints for processing payments and managing subscriptions. Uses Paystack for payment processing. This section details how users can subscribe to plans, view their payment history, and how administrators can manage payment records.

## Payment Processing

### POST /api/pay

Initiate payment for subscription. This endpoint starts the payment process by redirecting the user to the payment gateway.

#### Request Body:

-   `planType` (string, required): Identifier for the subscription plan being purchased. This corresponds to the `track` field in the `plans` table.
-   `amount` (integer, required): The amount to be paid for the subscription plan. This should be the amount in the smallest currency unit (e.g., kobo for Naira).
-   `callBackUrl` (string, required): A URL to redirect the user to after the payment process is completed (either successfully or unsuccessfully). This is used for handling the payment gateway's response.

```json
{
    "planType": "23",
    "amount": 500000,
    "callBackUrl": "https://your-app.com/payment-callback"
}
```

#### Response:

On successful initiation, returns the authorization URL for the payment gateway.

-   `authorization_url` (string): The URL provided by Paystack to complete the payment.

```json
{
    "authorization_url": "https://checkout.paystack.com/0peioxfhpn"
}
```

### GET /api/pay/callback

Payment processor callback URL (handled automatically). This endpoint is called by the payment gateway after a transaction is completed. It processes the payment details and updates the user's subscription status. It is not typically called directly by the client.

### GET /api/pay/reference/{reference}

Check payment status by reference. Allows verification of a payment's status using its unique reference ID.

#### Parameters:

-   `reference` (string, required): The unique reference ID of the payment transaction.

#### Response:

Returns details about the payment if found:

-   `exists` (boolean): Indicates if a payment record with the given reference was found.
-   `amount` (float): The amount paid in the transaction.
-   `reference` (string): The unique reference ID of the payment.
-   `planName` (string): The name of the subscription plan purchased.
-   `planType` (string): The identifier for the subscription plan.
-   `status` (string): The status of the payment transaction (e.g., 'Successful', 'Failed').
-   `message` (string): A message indicating the outcome of the check.
-   `active` (string): The current subscription status ('active' or 'inactive').

```json
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

Get authenticated user's payment history. Retrieves a list of all past payment transactions made by the logged-in user.

#### Response:

Returns a paginated list of payment records:

-   `data` (array): An array of payment objects, each containing:
    -   `id` (integer): Unique identifier for the payment record.
    -   `amount` (float): The amount paid.
    -   `status` (string): The status of the payment ('active', 'expired', etc.).
    -   `reference` (string): The transaction reference ID.
    -   `created_at` (string): Timestamp of when the payment was made.
    -   `plan` (object): Details of the subscription plan associated with the payment:
        -   `name` (string): The name of the plan.
        -   `duration` (string): The duration of the subscription (e.g., "30 days").
-   `meta` (object): Pagination metadata including `current_page`, `per_page`, and `total`.

```json
{
    "data": [
        {
            "id": 1,
            "amount": 5000.0,
            "status": "active",
            "reference": "7PVGX8MEK85E",
            "created_at": "2025-07-01T10:00:00Z",
            "plan": {
                "name": "Premium Monthly",
                "duration": "30 days"
            }
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 5
    }
}
```

### GET /api/pay/status

Check user's subscription status. Determines if the user currently has an active subscription.

#### Response:

-   `status` (string): Indicates the subscription status ('active' or 'inactive').
-   `message` (string): A message describing the subscription status.

```json
{
    "status": "active",
    "message": "User has an active subscription"
}
```

## Admin Endpoints

Requires admin privileges.

### GET /api/pay/all

List all payments with user details. Administrators can view all payment records across all users.

#### Query Parameters:

-   `per_page` (integer, optional): Number of payment records to display per page. Defaults to 15.
-   `all` (boolean, optional): If set to `true`, returns all payment records without pagination. Defaults to `false`.

#### Response:

Returns a paginated list of payment records, including user information:

-   `data` (array): An array of payment objects, each containing:
    -   `id` (integer): Unique identifier for the payment record.
    -   `amount` (float): The amount paid.
    -   `user` (object): Information about the user who made the payment:
        -   `name` (string): The user's name.
        -   `email` (string): The user's email address.
    -   `status` (string): The status of the payment ('completed', 'pending', etc.).
    -   `created_at` (string): Timestamp of when the payment was made.
-   `meta` (object): Pagination metadata if `all` is false.

```json
{
    "data": [
        {
            "id": 1,
            "amount": 5000.0,
            "user": {
                "name": "John Doe",
                "email": "john.doe@example.com"
            },
            "status": "completed",
            "created_at": "2025-07-01T10:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

## Status Codes

-   `200 OK`: Request successful.
-   `201 Created`: Resource successfully created.
-   `400 Bad Request`: The request was malformed or invalid (e.g., missing required fields, invalid amount).
-   `401 Unauthorized`: Authentication failed. The provided token is missing, invalid, or expired.
-   `403 Forbidden`: The authenticated user does not have the necessary permissions (e.g., not an admin).
-   `404 Not Found`: The requested resource (e.g., plan, payment reference) could not be found.
-   `500 Internal Server Error`: An unexpected error occurred on the server.

## Error Handling

Provides details about validation errors or other issues encountered during request processing.

```json
{
    "status": false,
    "message": "Payment failed",
    "error": "Plan not found"
}
```
