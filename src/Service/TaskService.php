<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
    ) {
    }

    /**
     * 
     * @param \App\Entity\User $user
     * 
     * @return Task[]
     */
    public function fetchByUser(User $user): array
    {
        return $this->taskRepository->findByUser($user);
    }

    public function create(Task $task, User $user): void
    {
        $task->setUserTask($user);
        $task->setCreatedAt(new \DateTimeImmutable());

        $this->taskRepository->create($task);
    }

    public function update(Task $task): void
    {
        $this->taskRepository->update($task);
    }

    public function delete(Task $task): void
    {
        $this->taskRepository->delete($task);
    }
}
