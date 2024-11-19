<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskRepository $taskRepository,
    ) {}

    public function fetchByUser(User $user): array
    {
        return $this->taskRepository->findBy(['user_task' => $user]);
    }

    public function create(Task $task, User $user): void
    {
        $task->setUserTask($user);
        $task->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function toggleStatus(Task $task, User $user): void
    {
        $task->setFinished(!$task->isFinished());
        $this->entityManager->flush();
    }

    public function update(Task $task): void
    {
        $this->entityManager->flush();
    }

    public function delete(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}