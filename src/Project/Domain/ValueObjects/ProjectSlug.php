<?php

declare(strict_types=1);

namespace Src\Project\Domain\ValueObjects;

use InvalidArgumentException;
use Src\Shared\Domain\ValueObjects\StringValueObject;

final readonly class ProjectSlug extends StringValueObject
{
    public function __construct(string $value)
    {
        parent::__construct(strtoupper($value));
    }

    protected function validate(): void
    {
        if (strlen($this->value) > 4) {
            throw new InvalidArgumentException('Slug cannot be longer than 4 characters');
        }
    }
}
