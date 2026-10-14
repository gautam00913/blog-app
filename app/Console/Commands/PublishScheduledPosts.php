<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publier les articles programmés dont la date de publication est atteinte';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $posts = Post::query()
            ->scheduled()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($posts as $post) {
            $post->update(['status' => 'published']);
            $count++;
        }

        if ($count > 0) {
            $this->info(sprintf('%d article(s) publié(s).', $count));
        }

        return self::SUCCESS;
    }
}
