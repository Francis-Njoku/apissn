<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post Notification</title>
</head>
<body>
    <h1>New Post: {{ $title }}</h1>
    @if($body)
        @php
            // Extract paragraphs from HTML
            preg_match_all('/<p>(.*?)<\/p>/s', $body, $matches);
            $allParagraphs = $matches[1];
            
            // Get first 2 paragraphs
            $firstTwoParagraphs = array_slice($allParagraphs, 0, 2);
        @endphp
        @foreach($firstTwoParagraphs as $paragraph)
            <p>{!! trim($paragraph) !!}</p>
        @endforeach
    @else
        <p>A new post has been published on our platform. Check it out at <a href="https://ftm.ng">ftm.ng</a>!</p>
    @endif

    @php
        // Map mediaType to URL path
        $pathPrefixMap = [
            'audio' => '/podcasts',
            'video' => '/videos',
            'bytes' => '/bytes',
            'text' => '/articles'
        ];

        // Get the path prefix, default to '/articles' if not found
        $pathPrefix = $pathPrefixMap[$mediaType] ?? '/articles';

        // Construct the full article URL
        $articleUrl = 'https://ftm.ng' . $pathPrefix . '/' . $slug;
    @endphp

    <p>Read the full article at <a href="{{ $articleUrl }}">{{ $articleUrl }}</a>.</p>
</body>
</html>
