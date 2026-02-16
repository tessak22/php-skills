<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Platform;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'platform',
        'platform_id',
        'author_name',
        'author_handle',
        'author_avatar_url',
        'content',
        'media_url',
        'post_url',
        'engagement_score',
        'is_featured',
        'is_hidden',
        'published_at',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'platform' => Platform::class,
            'is_featured' => 'boolean',
            'is_hidden' => 'boolean',
            'published_at' => 'datetime',
            'fetched_at' => 'datetime',
        ];
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('published_at');
    }

    public function scopeForPlatform($query, Platform $platform)
    {
        return $query->where('platform', $platform);
    }
}
