<?php
// app/Mail/NewPostMail.php

namespace App\Mail;

use DOMDocument;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Str;

class NewPostMail extends Mailable
{
    public $title;
    public $excerpt;
    public $articleUrl;
    public $siteUrl;

    /**
     * Media type to URL path mapping.
     *
     * @var array
     */
    private const MEDIA_PATH_MAP = [
        'audio' => '/podcasts',
        'video' => '/videos',
        'bytes' => '/bytes',
        'text' => '/articles',
    ];

    /**
     * Create a new message instance.
     *
     * @param  string  $title
     * @param  string  $body
     * @param  string  $slug
     * @param  string  $mediaType
     * @return void
     */
    public function __construct($title, $body, $slug, $mediaType)
    {
        $this->siteUrl = config('app.url', 'https://ftm.ng');
        $this->title = $title;
        $this->excerpt = $this->extractExcerpt($body);
        $this->articleUrl = $this->buildArticleUrl($slug, $mediaType);
    }

    /**
     * Extract first 2 paragraphs from HTML body using DOMDocument.
     *
     * @param  string  $body
     * @return array
     */
    private function extractExcerpt(string $body): array
    {
        if (empty($body)) {
            return [];
        }

        $dom = new DOMDocument();
        
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        
        // Load HTML with proper encoding
        $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $body,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        
        libxml_clear_errors();

        $paragraphs = [];
        
        foreach ($dom->getElementsByTagName('p') as $paragraph) {
            $text = trim($paragraph->textContent);
            
            if (!empty($text)) {
                $paragraphs[] = $text;
            }
            
            // Stop after collecting 2 paragraphs
            if (count($paragraphs) >= 2) {
                break;
            }
        }

        return $paragraphs;
    }

    /**
     * Build the full article URL based on media type.
     *
     * @param  string  $slug
     * @param  string  $mediaType
     * @return string
     */
    private function buildArticleUrl(string $slug, string $mediaType): string
    {
        $pathPrefix = self::MEDIA_PATH_MAP[$mediaType] ?? '/articles';
        return $this->siteUrl . $pathPrefix . '/' . $slug;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('FTM new post')
            ->view('emails.new_post')
            ->with([
                'title' => $this->title,
                'excerpt' => $this->excerpt,
                'articleUrl' => $this->articleUrl,
                'siteUrl' => $this->siteUrl,
            ]);
    }
}
