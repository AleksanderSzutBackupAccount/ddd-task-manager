<?php

declare(strict_types=1);


namespace Src\Identity\Application\UseCases\SyncUsers;

use Src\Identity\Application\Services\SyncUsersFromExternalService;

final readonly class UserSynchronizer
{
    public function __construct(private SyncUsersFromExternalService $syncUsersFromExternalService)
    {
    }

    public function sync(): void
    {
        $this->syncUsersFromExternalService->sync();
    }
}
