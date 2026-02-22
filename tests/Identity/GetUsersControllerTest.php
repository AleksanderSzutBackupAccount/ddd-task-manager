<?php

declare(strict_types=1);

namespace App\Tests\Identity;

use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetUsersControllerTest extends WebTestCase
{
    public function testGetUsersReturnsListOfUsers(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        /** @var UserRepository $repository */
        $repository = $container->get(UserRepository::class);
        /** @var TokenGeneratorInterface $tokenGenerator */
        $tokenGenerator = $container->get(TokenGeneratorInterface::class);

        $user1 = new User(
            UserId::generate(),
            new UserName('User One'),
            new UserEmail('user1@example.com'),
            new UserExternalId('1')
        );
        $user2 = new User(
            new UserId('00000000-0000-0000-0000-000000000002'),
            new UserName('User Two'),
            new UserEmail('user2@example.com'),
            new UserExternalId('2')
        );
        $repository->upsert($user1);
        $repository->upsert($user2);

        $token = $tokenGenerator->generate($user1);

        $client->request('GET', '/api/users', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertGreaterThanOrEqual(2, count($data));

        $emails = array_column($data, 'email');
        $this->assertContains('user1@example.com', $emails);
        $this->assertContains('user2@example.com', $emails);
    }
}
