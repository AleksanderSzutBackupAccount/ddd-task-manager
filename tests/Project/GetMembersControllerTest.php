<?php

declare(strict_types=1);

namespace App\Tests\Project;

use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;
use Src\Project\Domain\Project;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetMembersControllerTest extends WebTestCase
{
    public function testGetMembersReturnsListOfMembers(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var ProjectRepository $projectRepository */
        $projectRepository = $container->get(ProjectRepository::class);
        /** @var TokenGeneratorInterface $tokenGenerator */
        $tokenGenerator = $container->get(TokenGeneratorInterface::class);

        $userId = UserId::generate();
        $user = new User($userId, new UserName('Test User'), new UserEmail('test@example.com'), new UserExternalId('1'));
        $userRepository->upsert($user);

        $project = new Project(ProjectId::generate(), 'Test Project', new ProjectSlug('TEST'), [$userId->value]);
        $projectRepository->save($project);

        $token = $tokenGenerator->generate($user);

        $client->request(
            'GET',
            '/api/projects/TEST/members',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals('Test User', $data[0]['name']);
    }

    public function testGetMembersProjectNotFound(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var TokenGeneratorInterface $tokenGenerator */
        $tokenGenerator = $container->get(TokenGeneratorInterface::class);

        $user = new User(UserId::generate(), new UserName('Test User'), new UserEmail('test@example.com'), new UserExternalId('1'));
        $userRepository->upsert($user);
        $token = $tokenGenerator->generate($user);

        $client->request(
            'GET',
            '/api/projects/non-existent/members',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
