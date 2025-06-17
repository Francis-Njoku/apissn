<?php

// app/Mail/WelcomeMail.php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class WelcomeMail extends Mailable
{
    public $user;

    /**
     * Create a new message instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to Follow The Money (FTM)')
                    ->view('emails.welcome')
                    ->with([
                        'userName' => $this->user->first_name,
                    ]);
    }
}
