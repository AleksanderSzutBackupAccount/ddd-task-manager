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

final class MeControllerTest extends WebTestCase
{
    public function testMeReturnsUserDataForAuthenticatedUser(): void
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

        // Login to get token
        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'test@example.com'])
        );
        $data = json_decode($client->getResponse()->getContent(), true);
        $token = $data['token'];

        // Access /api/auth/me
        $client->request(
            'GET',
            '/api/auth/me',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($user->id, $data['id']);
        $this->assertEquals('Test User', $data['name']);
        $this->assertEquals('test@example.com', $data['email']);
    }

    public function testMeReturns401ForUnauthenticatedUser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/auth/me');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Unauthenticated', $data['message']);
    }
}
