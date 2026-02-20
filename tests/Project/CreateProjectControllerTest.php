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
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CreateProjectControllerTest extends WebTestCase
{
    public function testCreateProjectSuccessfully(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var ProjectRepository $projectRepository */
        $projectRepository = $container->get(ProjectRepository::class);
        /** @var TokenGeneratorInterface $tokenGenerator */
        $tokenGenerator = $container->get(TokenGeneratorInterface::class);

        $user = new User(
            UserId::generate(),
            new UserName('Test User'),
            new UserEmail('test@example.com'),
            new UserExternalId('1')
        );
        $userRepository->upsert($user);

        $token = $tokenGenerator->generate($user);

        $client->request(
            'POST',
            '/api/projects',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => 'New Project',
                'slug' => 'NEWP',
                'userIds' => [$user->id()->value],
            ])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $project = $projectRepository->findBySlug(new ProjectSlug('NEWP'));
        $this->assertNotNull($project);
        $this->assertEquals('New Project', $project->name());
    }

    public function testCreateProjectValidationError(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var TokenGeneratorInterface $tokenGenerator */
        $tokenGenerator = $container->get(TokenGeneratorInterface::class);

        $user = new User(
            UserId::generate(),
            new UserName('Test User'),
            new UserEmail('test@example.com'),
            new UserExternalId('1')
        );
        $userRepository->upsert($user);

        $token = $tokenGenerator->generate($user);

        $client->request(
            'POST',
            '/api/projects',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => '',
                'slug' => 'NEWP',
            ])
        );

        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }
}
