<?php

declare(strict_types=1);

namespace App\Tests\Identity;

use Src\Identity\Domain\User;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginControllerTest extends WebTestCase
{
    public function testLoginReturnsTokenForExistingUser(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        /** @var UserRepository $repository */
        $repository = $container->get(UserRepository::class);

        $user = new User(
            UserId::generate(),
            new UserName('Test User'),
            new UserEmail('test@example.com'),
            new UserExternalId('1')
        );
        $repository->upsert($user);

        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'test@example.com'])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertNotEmpty($data['token']);
    }

    public function testLoginReturnsErrorForNonExistentUser(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'nonexistent@example.com'])
        );

        // Based on LoginController.php, if user not found, LoginByEmailQueryHandler throws UserNotFoundException
        // We need to see how Symfony handles this exception.
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('user_not_found', $data['error']);
    }

    public function testLoginValidationErrors(): void
    {
        $client = static::createClient();

        // Missing email
        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );
        $this->assertEquals(422, $client->getResponse()->getStatusCode());

        // Invalid email format
        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'not-an-email'])
        );
        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }
}
