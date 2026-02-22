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
use Src\Project\Domain\Task;
use Src\Project\Domain\TaskWriteRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskSlug;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ChangeTaskStatusControllerTest extends WebTestCase
{
    public function testChangeTaskStatusSuccessfully(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var TokenGeneratorInterface $tokenGenerator */
        $tokenGenerator = $container->get(TokenGeneratorInterface::class);
        /** @var TaskWriteRepository $taskRepository */
        $taskRepository = $container->get(TaskWriteRepository::class);

        $userId = UserId::generate();
        $user = new User($userId, new UserName('Test User'), new UserEmail('test@example.com'), new UserExternalId('1'));
        $userRepository->upsert($user);

        $taskId = TaskId::generate();
        $task = Task::create(
            $taskId,
            ProjectId::generate(),
            new TaskSlug('TEST-1'),
            'Task One',
            'Description One',
            new TaskStatus(TaskStatus::TO_DO),
            $userId
        );

        $taskRepository->save($task);

        $token = $tokenGenerator->generate($user);

        $client->request(
            'PATCH',
            '/api/tasks/'.$taskId->value().'/status',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['status' => TaskStatus::DONE])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Status zadania został zmieniony', $data['message']);
    }

    public function testChangeTaskStatusValidationError(): void
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
            'PATCH',
            '/api/tasks/some-id/status',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['status' => ''])
        );

        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }
}
