<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    private TaskService $taskService;
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->taskService = new TaskService($this->entityManager, $this->taskRepository);
    }

    public function testFetchByUser(): void
    {
        $user = new User();
        $task = new Task();
        $task->setUserTask($user);

        $this->taskRepository
            ->method('findBy')
            ->with(['user_task' => $user])
            ->willReturn([$task]);

        $tasks = $this->taskService->fetchByUser($user);

        $this->assertCount(1, $tasks);
        $this->assertSame($task, $tasks[0]);
    }

    public function testCreate(): void
    {
        $user = new User();
        $task = new Task();

        $this->entityManager->expects($this->once())->method('persist')->with($task);
        $this->entityManager->expects($this->once())->method('flush');

        $this->taskService->create($task, $user);

        $this->assertSame($user, $task->getUserTask());
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());
    }
}
