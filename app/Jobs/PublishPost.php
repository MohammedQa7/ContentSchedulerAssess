<?php

namespace App\Jobs;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PublishPost implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $posts = Post::with('platforms')
            ->withoutGlobalScopes()
            ->where('scheduled_time', '<=', now())
            ->whereNull('published_at')
            ->get();


        if (sizeof($posts) > 0) {
            try {
                DB::beginTransaction();
                foreach ($posts as $post) {

                    // Update the post status
                    $post->update([
                        'published_at' => now(),
                        'status' => PostStatus::PUBLISHED->value
                    ]);

                    // Update the eash status for all attached platfomrs
                    foreach ($post->platforms as $platform) {
                        $post->platforms()->updateExistingPivot($platform->id, [
                            'platform_status' => PostStatus::PUBLISHED->value,
                        ]);
                    }
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
        }


    }
}