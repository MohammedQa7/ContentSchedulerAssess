<?php

namespace App\Actions;

use App\Enums\PostStatus;
use App\Models\Platform;

class AttachPostToPlatforms
{
    function handle($post, $platforms, $publish_status)
    {
        if ($publish_status != PostStatus::DRAFT->value) {
            foreach ($platforms as $single_platform) {
                $platform = Platform::where('type', $single_platform)->firstOrFail();
                $post->platforms()->attach($platform->id, [
                    'platform_status' => $publish_status
                ]);

            }
        }

    }
}