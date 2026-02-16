<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'install_command' => $this->install_command,
            'content' => $this->content,
            'source_url' => $this->source_url,
            'is_official' => $this->is_official,
            'is_featured' => $this->is_featured,
            'install_count' => $this->install_count,
            'compatible_agents' => $this->compatible_agents,
            'tags' => $this->tags,
            'author' => new AuthorResource($this->whenLoaded('author')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
