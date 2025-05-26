<?php

namespace App\Http\Resources;

use App\Enums\PostStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'platforms' => PlatformResource::collection($this->whenLoaded('platforms')),
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image_url ? Storage::disk('public')->url($this->image_url) : null,
            'imageOriginalPath' => $this->image_url,
            'scheduledDate' => $this->formatOnRoute($request, $this->scheduled_time),
            'publishedAt' => $this->published_at ? Carbon::parse($this->published_at)->diffForHumans() : null,
            'publishedAtRawDate' => Carbon::parse($this->published_at),
            'status' => $this->status,
            'tags' => $this->tags,

        ];
    }

    function formatOnRoute(Request $request, $date)
    {
        if ($request->routeIs('posts.edit')) {
            return $this->status->value == PostStatus::SCHEDULED->value ? $date : null;
        } else {
            return $date ? Carbon::parse($date)->diffForHumans() : null;
        }
    }
}