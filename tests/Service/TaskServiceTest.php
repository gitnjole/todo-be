<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    private TaskRepository|MockObject $taskRepositoryMock;
    private TaskService $taskService;
    private User $user;

    protected function setUp(): void
    {
        $this->taskRepositoryMock = $this->createMock(TaskRepository::class);

        $this->taskService = new TaskService(
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
            ->method('findByUser')
            ->with($this->user)
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

        $this->taskRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function (Task $persistedTask) use ($task) {
                $this->assertSame($this->user, $persistedTask->getUserTask());
                $this->assertInstanceOf(\DateTimeImmutable::class, $persistedTask->getCreatedAt());
                return true;
            }));

        $this->taskService->create($task, $this->user);
    }

    /**
     * Test updating a task
     */
    public function testUpdate(): void
    {
        $task = new Task();
        $task->setTitle('Updated Title');

        $this->taskRepositoryMock
            ->expects($this->once())
            ->method('update')
            ->with($task);

        $this->taskService->update($task);
    }

    /**
     * Test deleting a task
     */
    public function testDelete(): void
    {
        $task = new Task();

        $this->taskRepositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($task);

        $this->taskService->delete($task);
    }
}