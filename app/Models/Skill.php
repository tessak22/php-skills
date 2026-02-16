<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Skill extends Model
{
    use HasFactory, HasUlids, Searchable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'install_command',
        'content',
        'author_id',
        'category_id',
        'source_url',
        'is_official',
        'is_featured',
        'install_count',
        'compatible_agents',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'is_official' => 'boolean',
            'is_featured' => 'boolean',
            'compatible_agents' => 'array',
            'tags' => 'array',
        ];
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'content' => $this->content,
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfficial($query)
    {
        return $query->where('is_official', true);
    }

    public function scopeOrdered($query, string $sort = 'newest')
    {
        return match ($sort) {
            'installs' => $query->orderByDesc('install_count'),
            default => $query->latest(),
        };
    }
}
