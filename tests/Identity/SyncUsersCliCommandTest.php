<?php

declare(strict_types=1);

namespace App\Tests\Identity;

use Src\Identity\Application\External\ExternalUser;
use Src\Identity\Application\Ports\ExternalUserProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class SyncUsersCliCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;
    private $providerMock;

    protected function setUp(): void
    {
        self::bootKernel();

        // Tworzymy mocka providera
        $this->providerMock = $this->createMock(ExternalUserProvider::class);

        // Podmieniamy serwis w kontenerze na mocka
        // UWAGA: Serwis musi być publiczny w testach lub używamy testowego kontenera
        self::getContainer()->set(ExternalUserProvider::class, $this->providerMock);

        $application = new Application(self::$kernel);
        $command = $application->find('identity:sync-users');
        $this->commandTester = new CommandTester($command);
    }

    public function testItSyncsUsersViaCliCommand(): void
    {
        $externalUser = new ExternalUser(
            externalId: '1',
            username: 'johndoe',
            email: 'john@example.com',
            name: 'John Doe'
        );

        $this->providerMock->expects($this->once())
            ->method('fetchUsers')
            ->willReturn([$externalUser]);

        $this->commandTester->execute([]);

        $this->commandTester->assertCommandIsSuccessful();
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Synced 1 users.', $output);

        // Odpowiednik assertDatabaseHas z Doctrine
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManager->getRepository(\Src\Identity\Infrastructure\Symfony\Entity\UserEntity::class)->findOneBy(['email' => 'john@example.com']);

        $this->assertNotNull($user);
        // Możesz dodać więcej asercji dla konkretnych pól
    }

    public function testItHandlesEmptySync(): void
    {
        $this->providerMock->expects($this->once())
            ->method('fetchUsers')
            ->willReturn([]);

        $this->commandTester->execute([]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Synced 0 users.', $this->commandTester->getDisplay());
    }

    public function testItFailsWhenProviderThrowsException(): void
    {
        $this->providerMock->expects($this->once())
            ->method('fetchUsers')
            ->willThrowException(new \Exception('External service failure'));

        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('External service failure', $this->commandTester->getDisplay());
    }
}
