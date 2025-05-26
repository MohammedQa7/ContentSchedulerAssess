<?php

namespace App\Services;

use App\Enums\PostStatus;
use App\Http\Resources\PlatformResource;
use App\Models\Platform;
use Illuminate\Support\Facades\Cache;


class PlatformService
{
    function getPlatfroms()
    {
        return $this->getOrCachePlatforms();
    }

    function getEnabledPlatforms()
    {
        return Platform::withWhereHas(
            'user',
            function ($query) {
                return $query->where('user_id', auth()->id());
            }
        )->get();
    }


    function getUnAttachedPlatforms($post)
    {
        return Platform::withWhereHas(
            'user',
            function ($query) {
                return $query->where('user_id', auth()->id());
            }
        )
            ->whereDoesntHave('posts', function ($query) use ($post) {
                $query
                    ->withoutGlobalScopes()
                    ->where('post_id', $post->id);
            })->get();
    }
    function getAttachedPlatforms($post)
    {

        return Platform::whereHas('posts', function ($query) use ($post) {
            $query
                ->withoutGlobalScopes()
                ->where('post_id', $post->id);
        })->get();
    }

    function getPostCountForEachStatus()
    {
        return Cache::has('post_count_per_platform')
            ? Cache::get('post_count_per_platform')
            : Cache::remember('post_count_per_platform', now()->addDays(3), function () {
                return PlatformResource::collection(
                    Platform::withCount([
                        'posts',
                        'posts as published_posts_count' => function ($query) {
                            $query
                                ->withoutGlobalScopes()
                                ->where('status', PostStatus::PUBLISHED->value);
                        },
                        'posts as scheduled_posts_count' => function ($query) {
                            $query
                                ->withoutGlobalScopes()
                                ->where('status', PostStatus::SCHEDULED->value);
                        },
                    ])->get()
                );

            });
    }

    private function getOrCachePlatforms()
    {
        return Cache::has('platforms')
            ? Cache::get('platforms')
            : Cache::remember('platforms', now()->addDays(3), function () {
                return PlatformResource::collection(Platform::get());
            });
    }
}