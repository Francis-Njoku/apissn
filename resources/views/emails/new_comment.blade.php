<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Comment Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        h1 {
            color: #2c3e50;
            margin-top: 0;
            font-size: 24px;
        }
        p {
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .comment-box {
            background-color: #f9f9f9;
            padding: 20px;
            border-left: 4px solid #e74c3c;
            margin: 20px 0;
        }
        .comment-box p {
            margin: 0 0 10px 0;
        }
        .comment-box p:last-child {
            margin-bottom: 0;
        }
        .author-info {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .author-info strong {
            color: #2c3e50;
        }
        .newsletter-info {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .newsletter-info strong {
            color: #2980b9;
        }
        .cta-button {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .cta-button:hover {
            background-color: #2980b9;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777777;
        }
        .footer a {
            color: #3498db;
            text-decoration: none;
        }
        .timestamp {
            font-size: 12px;
            color: #999999;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; font-size: 28px;">FTM</h2>
        </div>
        
        <div class="content">
            <h1>New Comment Pending Moderation</h1>
            
            <p>A new comment has been submitted on the platform and is awaiting moderation.</p>
            
            <div class="newsletter-info">
                <p><strong>Newsletter:</strong> {{ $newsletterTitle }}</p>
            </div>
            
            <div class="author-info">
                <p><strong>Author:</strong> {{ $authorName }}</p>
                <p><strong>Email:</strong> {{ $authorEmail }}</p>
            </div>
            
            <div class="comment-box">
                <p><strong>Comment:</strong></p>
                <p>{!! nl2br(e($commentContent)) !!}</p>
            </div>
            
            <div class="timestamp">
                <p>Submitted: {{ $createdAt->format('F j, Y \a\t g:i A') }}</p>
            </div>
            
            <p style="text-align: center;">
                <a href="{{ $adminDashboardUrl }}" class="cta-button">Review Comments</a>
            </p>
            
            <p style="margin-top: 30px; font-size: 14px; color: #666666;">
                Or visit the comments dashboard directly at <a href="{{ $adminDashboardUrl }}">{{ $adminDashboardUrl }}</a>
            </p>
        </div>
        
        <div class="footer">
            <p>
                You're receiving this email because you are an admin on FTM.<br>
                <a href="{{ $siteUrl }}">Visit our website</a>
            </p>
        </div>
    </div>
</body>
</html>
