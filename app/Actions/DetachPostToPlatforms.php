<?php

namespace App\Actions;

use App\Enums\PostStatus;
use App\Models\Platform;

class DetachPostToPlatforms
{
    function handle($post, $platforms)
    {
        foreach ($platforms as $single_platform) {
            $platform = Platform::where('type', $single_platform)->firstOrFail();
            $post->platforms()->detach($platform->id);
        }

    }
}