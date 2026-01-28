<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post Notification</title>
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
        .excerpt {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #3498db;
            margin: 20px 0;
        }
        .excerpt p {
            margin: 0 0 10px 0;
        }
        .excerpt p:last-child {
            margin-bottom: 0;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; font-size: 28px;">FTM</h2>
        </div>
        
        <div class="content">
            <h1>New Post: {{ $title }}</h1>
            
            @if(!empty($excerpt))
                <div class="excerpt">
                    @foreach($excerpt as $paragraph)
                        <p>{!! $paragraph !!}</p>
                    @endforeach
                </div>
            @else
                <p>A new post has been published on our platform.</p>
            @endif
            
            <p style="text-align: center;">
                <a href="{{ $articleUrl }}" class="cta-button">Read Full Article</a>
            </p>
            
            <p style="margin-top: 30px; font-size: 14px; color: #666666;">
                Or visit us directly at <a href="{{ $articleUrl }}">{{ $articleUrl }}</a>
            </p>
        </div>
        
        <div class="footer">
            <p>
                You're receiving this email because you subscribed to FTM.<br>
                <a href="{{ $siteUrl }}">Visit our website</a> | 
                {{-- <a href="{{ $siteUrl }}/unsubscribe">Unsubscribe</a> --}}
            </p>
        </div>
    </div>
</body>
</html>
