<?php
// app/Mail/NewPostMail.php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class NewPostMail extends Mailable
{
    public $title;

    /**
     * Create a new message instance.
     *
     * @param  string  $title
     * @return void
     */
    public function __construct($title)
    {
        $this->title = $title;
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
                    ->with('title', $this->title);
    }
}
