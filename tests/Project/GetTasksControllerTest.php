<?php

declare(strict_types=1);

namespace App\Tests\Project;

use Doctrine\ORM\EntityManagerInterface;
use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;
use Src\Project\Domain\Project;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\Task;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskSlug;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Project\Infrastructure\Symfony\Entity\TaskEntity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetTasksControllerTest extends WebTestCase
{
    public function testGetTasksReturnsListOfTasks(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var ProjectRepository $projectRepository */
        $projectRepository = $container->get(ProjectRepository::class);
        /** @var TokenGeneratorInterface $tokenGenerator */
        $tokenGenerator = $container->get(TokenGeneratorInterface::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $userId = UserId::generate();
        $user = new User($userId, new UserName('Test User'), new UserEmail('test@example.com'), new UserExternalId('1'));
        $userRepository->upsert($user);

        $projectId = ProjectId::generate();
        $project = new Project($projectId, 'Test Project', new ProjectSlug('TEST'), [$userId->value]);
        $projectRepository->save($project);

        $task = Task::create(
            TaskId::generate(),
            $projectId,
            new TaskSlug('TEST-1'),
            'Task One',
            'Description One',
            new TaskStatus(TaskStatus::TO_DO),
            $userId
        );

        $taskEntity = TaskEntity::fromDomain($task);
        $entityManager->persist($taskEntity);
        $entityManager->flush();

        $token = $tokenGenerator->generate($user);

        $client->request(
            'GET',
            '/api/projects/TEST/tasks',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals('Task One', $data[0]['name']);
    }

    public function testGetTasksProjectNotFound(): void
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
            '/api/projects/non-existent/tasks',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
