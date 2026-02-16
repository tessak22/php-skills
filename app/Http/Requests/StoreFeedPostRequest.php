<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreFeedPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
            'post_url' => ['required', 'url', 'max:500'],
            'platform' => ['nullable', new Enum(Platform::class)],
            'author_name' => ['nullable', 'string', 'max:255'],
            'author_handle' => ['nullable', 'string', 'max:255'],
        ];
    }
}
