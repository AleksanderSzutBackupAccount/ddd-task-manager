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

final class CreateTaskControllerTest extends WebTestCase
{
    public function testCreateTaskSuccessfully(): void
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
            'POST',
            '/api/projects/TEST/tasks',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => 'New Task',
                'description' => 'New Description',
                'assigned_user_id' => $userId->value,
            ])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Zadanie zostało utworzone', $data['message']);
    }

    public function testCreateTaskValidationError(): void
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
            'POST',
            '/api/projects/TEST/tasks',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => '',
                'description' => 'New Description',
            ])
        );

        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }
}
