<?php

// app/Jobs/SendNewPostEmail.php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Models\User;
use App\Mail\NewPostMail;

class SendNewPostEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $title;
    public $body;

    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get unique user emails from the payment table by joining with the users table
        $userEmails = DB::table('payment')
            ->join('users', 'payment.user_id', '=', 'users.id')
            ->select('users.email')
            ->distinct()
            ->get(); // Fetch emails

        // Send emails in batches of 100
        $userEmails->chunk(100)->each(function ($chunk) {
            foreach ($chunk as $user) {
                // Send email
                Mail::to($user->email)->send(new NewPostMail($this->title, $this->body));
            }
        });
    }
}
