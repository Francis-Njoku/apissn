<?php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;

class NewCommentMail extends Mailable
{
    public $comment;
    public $authorName;
    public $authorEmail;
    public $commentContent;
    public $newsletterTitle;
    public $adminDashboardUrl;
    public $siteUrl;
    public $createdAt;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        $this->siteUrl = config('app.url', 'https://ftm.ng');
        $this->adminDashboardUrl = $this->siteUrl . '/admin/comments';

        // Load relationships
        $comment->load(['user', 'newsletter']);

        // Get author information
        if ($comment->user) {
            $this->authorName = $comment->user->name;
            $this->authorEmail = $comment->user->email;
        } else {
            $this->authorName = $comment->author_name ?? 'Guest';
            $this->authorEmail = $comment->author_email ?? 'Not provided';
        }

        $this->commentContent = $comment->content;
        $this->newsletterTitle = $comment->newsletter ? $comment->newsletter->title : 'Unknown Newsletter';
        $this->createdAt = $comment->created_at;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('FTM - New Comment Pending Moderation')
            ->view('emails.new_comment')
            ->with([
                'authorName' => $this->authorName,
                'authorEmail' => $this->authorEmail,
                'commentContent' => $this->commentContent,
                'newsletterTitle' => $this->newsletterTitle,
                'adminDashboardUrl' => $this->adminDashboardUrl,
                'siteUrl' => $this->siteUrl,
                'createdAt' => $this->createdAt,
            ]);
    }
}
