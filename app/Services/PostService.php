<?php

namespace App\Services;

use App\Enums\PostStatus;
use App\Http\Resources\PlatformResource;
use App\Models\Platform;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;


class PostService
{
    function isScheduledTimeGreaterThanNow($date)
    {
        if ($date) {
            return $date > now();
        } else {
            return false;
        }
    }


    function getPostCount()
    {
        return Cache::has('post_count')
            ? Cache::get('post_count')
            : Cache::remember('post_count', now()->addDays(3), function () {
                return Post::count();
            });
    }

    function getPublishedPostCount()
    {


        return Cache::has('published_post_count')
            ? Cache::get('published_post_count')
            : Cache::remember('published_post_count', now()->addDays(3), function () {
                return Post::whereNotNull('published_at')
                    ->where('status', PostStatus::PUBLISHED->value)
                    ->count();
            });
    }

    function getSchedualedPostCount()
    {

        return Cache::has('schedualed_post_count')
            ? Cache::get('schedualed_post_count')
            : Cache::remember('schedualed_post_count', now()->addDays(3), function () {
                return Post::withoutGlobalScopes()
                    ->whereNotNull('scheduled_time')
                    ->whereNull('published_at')
                    ->where('status', PostStatus::SCHEDULED->value)
                    ->count();
            });
    }

}