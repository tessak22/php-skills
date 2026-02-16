<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialPostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform' => $this->platform->value,
            'platform_label' => $this->platform->label(),
            'platform_id' => $this->platform_id,
            'author_name' => $this->author_name,
            'author_handle' => $this->author_handle,
            'author_avatar_url' => $this->author_avatar_url,
            'content' => $this->content,
            'media_url' => $this->media_url,
            'post_url' => $this->post_url,
            'engagement_score' => $this->engagement_score,
            'is_featured' => $this->is_featured,
            'is_hidden' => $this->is_hidden,
            'published_at' => $this->published_at,
            'fetched_at' => $this->fetched_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
