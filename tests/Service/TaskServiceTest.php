<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManagerMock;
    private TaskRepository|MockObject $taskRepositoryMock;
    private TaskService $taskService;
    private User $user;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->taskRepositoryMock = $this->createMock(TaskRepository::class);

        $this->taskService = new TaskService(
            $this->entityManagerMock,
            $this->taskRepositoryMock
        );

        $this->user = new User();
    }

    /**
     * Test fetching tasks by user
     */
    public function testFetchByUser(): void
    {
        $tasks = [new Task(), new Task()];

        $this->taskRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with(['user_task' => $this->user])
            ->willReturn($tasks);

        $result = $this->taskService->fetchByUser($this->user);

        $this->assertCount(2, $result);
        $this->assertEquals($tasks, $result);
    }

    /**
     * Test creating a new task
     */
    public function testCreate(): void
    {
        $task = new Task();

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Task $persistedTask) use ($task) {
                $this->assertSame($this->user, $persistedTask->getUserTask());
                $this->assertInstanceOf(\DateTimeImmutable::class, $persistedTask->getCreatedAt());
                return true;
            }));

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->taskService->create($task, $this->user);
    }

    /**
     * Test updating a task
     */
    public function testUpdate(): void
    {
        $task = new Task();
        $task->setTitle('Updated Title');

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->taskService->update($task);
    }

    /**
     * Test deleting a task
     */
    public function testDelete(): void
    {
        $task = new Task();

        $this->entityManagerMock
            ->expects($this->once())
            ->method('remove')
            ->with($task);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->taskService->delete($task);
    }
}