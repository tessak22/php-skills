<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:skills,name'],
            'description' => ['required', 'string'],
            'install_command' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'string', 'exists:categories,id'],
            'compatible_agents' => ['nullable', 'array'],
            'compatible_agents.*' => ['string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'source_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
