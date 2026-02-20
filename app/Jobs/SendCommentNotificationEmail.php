<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Models\Comment;
use App\Models\User;
use App\Mail\NewCommentMail;

class SendCommentNotificationEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $comment;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get all users with admin role
        $adminUsers = User::whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();

        // Send emails in batches of 100
        $adminUsers->chunk(100)->each(function ($chunk) {
            foreach ($chunk as $admin) {
                // Only send if admin has an email
                if ($admin->email) {
                    Mail::to($admin->email)->send(new NewCommentMail($this->comment));
                }
            }
        });
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        // Log the failure
        Log::error('Failed to send comment notification email', [
            'comment_id' => $this->comment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
