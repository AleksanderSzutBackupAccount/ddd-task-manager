<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\GetUsers;

use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<UserResponse>
 */
final readonly class GetUsersQuery implements QueryInterface
{
    public function __construct() {}
}
