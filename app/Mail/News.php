<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class News extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $body)
    {
        $this->name = $data;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    /*
     public function build()
    {
        return $this->view('view.name');
    }
    */

    public function build()
    {
        $det = $this->name;

        return $this->from($det['email'])->subject($det['subject'])->view('emails.news')
            ->with(['name' => $this->name, 'body' => $this->body]);
    }
}
