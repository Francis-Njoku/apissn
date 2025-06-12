<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comment;

class MigrateCommentsToNewsletter extends Command
{
    protected $signature = 'comments:migrate-to-newsletter';
    protected $description = 'Migrate polymorphic comments to newsletter-only relationship';

    public function handle()
    {
        $this->info('Starting comments migration...');

        // Migrate all comments (since polymorphic columns are gone)
        $comments = Comment::cursor();

        $migrated = 0;
        $skipped = 0;

        foreach ($comments as $comment) {
            try {
                // Only set newsletter_id if it's null
                if (is_null($comment->newsletter_id)) {
                    $comment->update([
                        'newsletter_id' => $comment->commentable_id ?? null,
                    ]);
                    $migrated++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to migrate comment {$comment->id}: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->info("Migration complete. Migrated: {$migrated}, Skipped: {$skipped}");

        if ($skipped > 0) {
            $this->warn("Some comments were skipped. Check the logs for details.");
        }
    }
}
