<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $clientUrl;

    /**
     * Create a new message instance.
     *
     * @param string $userName
     * @param string $userEmail
     * @return void
     */
    public function __construct($userName, $userEmail)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->clientUrl = config('app.client', 'https://FTM.ng');

        // Set queue connection and queue name for better management
        $this->onQueue('emails');
        $this->onConnection(config('queue.default', 'database'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to Follow The Money (FTM) - Account Created!')
            ->view('emails.welcome');
    }
}
