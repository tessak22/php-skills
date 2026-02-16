<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('platform');
            $table->string('platform_id');
            $table->string('author_name');
            $table->string('author_handle');
            $table->string('author_avatar_url')->nullable();
            $table->text('content');
            $table->string('media_url')->nullable();
            $table->string('post_url');
            $table->integer('engagement_score')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('published_at');
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique(['platform', 'platform_id']);
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
