<?php
// app/Mail/NewPostMail.php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class NewPostMail extends Mailable
{
    public $title;
    public $body;

    /**
     * Create a new message instance.
     *
     * @param  string  $title
     * @param  string  $body
     * @return void
     */
    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
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
            ->with('body', $this->body);
    }
}
