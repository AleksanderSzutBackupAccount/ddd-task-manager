<?php

declare(strict_types=1);

namespace Src\Project\Domain\ValueObjects;

use InvalidArgumentException;
use Src\Shared\Domain\ValueObjects\StringValueObject;

final readonly class TaskSlug extends StringValueObject
{
    public function __construct(string $value)
    {
        parent::__construct(strtoupper($value));
    }

    protected function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('Task slug cannot be empty');
        }

        if (! preg_match('/^[A-Z0-9]{1,4}-\d+$/', $this->value)) {
            throw new InvalidArgumentException(sprintf('Invalid task slug format: %s', $this->value));
        }
    }
}
