<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('skills', 'name')->ignore($this->route('skill'))],
            'description' => ['sometimes', 'string'],
            'install_command' => ['sometimes', 'string', 'max:500'],
            'content' => ['sometimes', 'string'],
            'category_id' => ['nullable', 'string', 'exists:categories,id'],
            'compatible_agents' => ['nullable', 'array'],
            'compatible_agents.*' => ['string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'source_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
