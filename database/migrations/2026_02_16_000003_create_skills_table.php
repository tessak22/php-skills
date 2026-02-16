<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('install_command');
            $table->text('content');
            $table->foreignUlid('author_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_url')->nullable();
            $table->boolean('is_official')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('install_count')->default(0);
            $table->json('compatible_agents')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index('install_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
