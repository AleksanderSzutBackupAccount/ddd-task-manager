<?php

declare(strict_types=1);

namespace Src\Project\Domain\ValueObjects;

use Src\Shared\Domain\ValueObjects\StringValueObject;

final readonly class TaskStatus extends StringValueObject
{
    public const TO_DO = 'To Do';

    public const IN_PROGRESS = 'In Progress';

    public const DONE = 'Done';

    public const VALID_STATUSES = [
        self::TO_DO,
        self::IN_PROGRESS,
        self::DONE,
    ];

    protected function validate(): void
    {
        if (!in_array($this->value, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid task status: %s', $this->value));
        }
    }

    public static function toDo(): self
    {
        return new self(self::TO_DO);
    }
}
