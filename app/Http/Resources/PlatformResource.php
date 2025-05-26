<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'enabled' => $this->isPlatfromEnabled(),
            "postsCount" => $this->posts_count ?? null,
            "publishedPostsCount" => $this->published_posts_count ?? null,
            "scheduledPostsCount" => $this->scheduled_posts_count ?? null,
        ];
    }
}