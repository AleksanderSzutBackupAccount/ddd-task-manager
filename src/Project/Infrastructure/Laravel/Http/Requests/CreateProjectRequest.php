<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string $slug
 * @property string[] $userIds
 */
final class CreateProjectRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:projects,slug', 'max:4',
                'regex:/^[a-z]+$/'],
            'userIds' => ['sometimes', 'array'],
            'userIds.*' => ['uuid', 'exists:users,id'],
        ];
    }
}
