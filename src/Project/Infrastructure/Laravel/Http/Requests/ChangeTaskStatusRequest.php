<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Src\Project\Domain\ValueObjects\TaskStatus;

/**
 * @property string $status
 */
final class ChangeTaskStatusRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => [Rule::in(TaskStatus::VALID_STATUSES)],
        ];
    }
}
