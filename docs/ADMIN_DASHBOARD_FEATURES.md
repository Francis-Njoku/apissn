# Admin Dashboard Features Documentation

## Table of Contents

-   [Overview](#overview)
-   [User Management](#user-management)
-   [Content Management](#content-management)
-   [Subscription Management](#subscription-management)
-   [Comment Moderation](#comment-moderation)
-   [Investment Tools](#investment-tools)
-   [Metrics & Reporting](#metrics--reporting)

## Overview

This document details all administrative features available through the dashboard API endpoints. All endpoints require JWT authentication and admin privileges.

---

## User Management

### Endpoints

-   `GET /admin/users` - List users with pagination
-   `POST /admin/users/create` - Create new users

**Capabilities:**

-   Filter users by role
-   Set user roles during creation
-   View user status (approved/pending)

### Subscriber Status Filters

The user management API now supports `subscriber_status` parameter:

-   `active`: Users with active subscriptions
-   `never_subscribed`: Registered users without any subscription history
-   `expired_non_renewed`: Users with expired subscriptions who haven't renewed

Example request:

```
GET /admin/users?subscriber_status=never_subscribed
```

[Detailed API](USER_ACCOUNT_API.md)

---

## Content Management

### Endpoints

-   `POST /admin/articles/add` - Create articles
-   `PUT /admin/articles/update/{slug}` - Update articles
-   `GET /admin/media` - List media files
-   `POST /admin/media/upload` - Upload media

**Capabilities:**

-   Manage articles with featured images
-   Control publication status
-   Bulk update image paths

[Detailed API](CONTENT_API.md)

---

## Subscription Management

### Endpoints

-   `GET /api/pay/all` - List all payments
-   `GET /api/pay/reference/{reference}` - Check payment status

**Capabilities:**

-   View payment history
-   Verify subscription status
-   Track payment methods

[Detailed API](SUBSCRIPTION_API.md)

---

## Comment Moderation

### Endpoints

-   `GET /api/admin/comments/pending` - List pending comments
-   `PATCH /api/admin/comments/{comment}/moderate` - Moderate comments

**Capabilities:**

-   Approve/reject comments
-   Mark comments as spam
-   View comment threads

[Detailed API](COMMUNITY_API.md)

---

## Investment Tools

### Endpoints

-   `POST /api/admin/stockpicks` - Create stock picks
-   `PATCH /api/admin/stockpicks/{stockPick}/price` - Update stock prices
-   `POST /api/admin/stockpicks/batch-update-prices` - Batch update prices

**Capabilities:**

-   Manage investment recommendations
-   Track stock performance
-   Associate picks with newsletters

[Detailed API](INVESTMENT_API.md)

---

## Metrics & Reporting

### Endpoints

-   `GET /admin/metrics/user-funnel` - User acquisition funnel
-   `GET /admin/metrics/subscription-health` - Subscription KPIs
-   `GET /admin/metrics/cohorts/{period}` - Cohort retention
-   `GET /admin/metrics/engagement/{user_id?}` - Feature engagement
-   `GET /admin/metrics/payment-analytics` - Payment metrics

**Capabilities:**

-   Track conversion rates
-   Monitor churn and revenue
-   Analyze user engagement
-   Payment performance reporting

[Detailed API](METRICS_API.md)

---

## System Administration

-   Role-based access control
-   Audit logging
-   Data export capabilities
-   System health monitoring

All endpoints protected by `auth:api` and `admin` middleware.
