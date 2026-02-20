<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string $description
 * @property string $assigned_user_id
 */
final class CreateTaskRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_user_id' => 'nullable|uuid',
        ];
    }
}
