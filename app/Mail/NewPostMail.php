<?php
// app/Mail/NewPostMail.php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class NewPostMail extends Mailable
{
    public $title;
    public $body;
    public $slug;
    public $mediaType;

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
        $this->title = $title;
        $this->body = $body;
        $this->slug = $slug;
        $this->mediaType = $mediaType;
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
            ->with('title', $this->title)
            ->with('body', $this->body)
            ->with('slug', $this->slug)
            ->with('mediaType', $this->mediaType);
    }
}
