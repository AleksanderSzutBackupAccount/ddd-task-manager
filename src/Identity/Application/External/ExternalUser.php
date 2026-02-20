<?php

declare(strict_types=1);

namespace Src\Identity\Application\External;

/**
 * @description external id as string because when provider of import change id not always be int(e.g., Uuid)
 */
final readonly class ExternalUser
{
    public function __construct(
        public string $externalId,
        public string $username,
        public string $email,
        public string $name,
    ) {
    }
}
