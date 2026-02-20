<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Symfony\Bus;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reindex;

final readonly class CallableFirstParameterExtractor
{
    public static function forCallables(iterable $callables): array
    {
        return map(self::unflatten(), reindex(self::classExtractor(new self()), $callables));
    }

    private static function classExtractor(self $parameterExtractor): callable
    {
        return static fn (object $handler): ?string => $parameterExtractor->extract($handler);
    }

    private static function unflatten(): callable
    {
        return static fn (mixed $value): array => [$value];
    }

    public function extract(object $class): ?string
    {
        $reflector = new \ReflectionClass($class);
        $method = $reflector->getMethod('__invoke');

        if ($this->hasOnlyOneParameter($method)) {
            return $this->firstParameterClassFrom($method);
        }

        return null;
    }

    private function firstParameterClassFrom(\ReflectionMethod $method): string
    {
        /** @var \ReflectionNamedType|null $fistParameterType */
        $fistParameterType = $method->getParameters()[0]->getType();

        if (null === $fistParameterType) {
            throw new \LogicException('Missing type hint for the first parameter of __invoke');
        }

        return $fistParameterType->getName();
    }

    private function hasOnlyOneParameter(\ReflectionMethod $method): bool
    {
        return 1 === $method->getNumberOfParameters();
    }
}
