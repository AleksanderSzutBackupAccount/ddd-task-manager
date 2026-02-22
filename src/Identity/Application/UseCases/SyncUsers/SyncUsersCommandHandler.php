<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\SyncUsers;

final readonly class SyncUsersCommandHandler
{
    public function __construct(private UserSynchronizer $synchronizer)
    {
    }

    public function handle(SyncUsersCommand $command): void
    {
        $this->synchronizer->sync();
    }
}
