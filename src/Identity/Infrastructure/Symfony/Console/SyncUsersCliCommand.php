<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Symfony\Console;

use Src\Identity\Application\Services\SyncUsersFromExternalService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'identity:sync-users',
    description: 'Sync users from external provider (JSONPlaceholder) into local database'
)]
final class SyncUsersCliCommand extends Command
{
    public function __construct(
        private readonly SyncUsersFromExternalService $sync,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $count = $this->sync->sync();

            $io->success(sprintf('Synced %d users.', $count));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
