<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObjects;

use Ramsey\Uuid\Uuid;
use Src\Shared\Domain\ComparableInterface;

abstract readonly class UuidValueObject implements ComparableInterface, \Stringable, ValueObjectInterface
{
    final public function __construct(public string $value)
    {
        $this->validate();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function validate(): void
    {
        if (!Uuid::isValid($this->value)) {
            throw new \InvalidArgumentException('Invalid UUID: '.$this->value);
        }
    }

    public static function fromNullable(?string $value): ?static
    {
        if (!$value) {
            return null;
        }

        return new static($value);
    }

    final public static function generate(): static
    {
        return new static(Uuid::uuid4()->toString());
    }

    /**
     * @param self $compare
     */
    public function equals(ComparableInterface $compare): bool
    {
        return $this->value === $compare->value;
    }
}
