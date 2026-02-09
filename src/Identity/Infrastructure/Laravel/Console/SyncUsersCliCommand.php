<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel\Console;

use Illuminate\Console\Command;
use Src\Identity\Application\Services\SyncUsersFromExternalService;
use Src\Shared\Domain\Exceptions\InvalidValueObjectException;

final class SyncUsersCliCommand extends Command
{
    protected $signature = 'identity:sync-users';
    protected $description = 'Sync users from external provider (JSONPlaceholder) into local database';


    /**
     * @throws InvalidValueObjectException
     */
    public function handle(SyncUsersFromExternalService $sync): int
    {
        $count = $sync->sync();

        $this->info(sprintf('Synced %d users.', $count));

        return self::SUCCESS;
    }
}
