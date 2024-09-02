<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Agrotech extends Mailable
{
    use Queueable, SerializesModels;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->name = $data;
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

        return $this->from($det['email'])->subject($det['subject'])->view('emails.agrotech')
            ->with(['name' => $this->name]);
    }
}
