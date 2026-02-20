<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure\Symfony\Persistence;

use Src\Project\Domain\Events\TaskCreated;
use Src\Shared\Domain\EventStore\EventStore;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineEventStoreTest extends KernelTestCase
{
    private ?EventStore $eventStore = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->eventStore = $container->get(EventStore::class);
    }

    public function testItShouldAppendAndRetrieveEvents(): void
    {
        $aggregateId = 'test-aggregate-id';
        $event = new TaskCreated(
            $aggregateId,
            'project-id',
            'task-slug',
            'Task Name',
            'Task Description',
            'todo',
            'user-id'
        );

        $this->eventStore->append([$event]);

        $events = $this->eventStore->getEventsFor($aggregateId);

        $this->assertCount(1, $events);
        $this->assertInstanceOf(TaskCreated::class, $events[0]);
        $this->assertEquals($aggregateId, $events[0]->aggregateId());
    }
}
