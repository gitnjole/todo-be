<?php

namespace App\MessageHandler;

use App\Message\TaskDeletionMessage;
use App\Repository\TaskRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TaskDeletionMessageHandler
{
    private TaskRepository $taskRepository;
    private LoggerInterface $logger;

    public function __construct(
        TaskRepository $taskRepository,
        LoggerInterface $logger
    ) {
        $this->taskRepository = $taskRepository;
        $this->logger = $logger;
    }

    public function __invoke(TaskDeletionMessage $message): void
    {
        $olderThan = $message->getOlderThan();
        
        $count = $this->taskRepository->deleteTasksOlderThan($olderThan);
        
        $this->logger->info(sprintf(
            'Deleted %d tasks created before %s',
            $count,
            $olderThan->format('Y-m-d H:i:s')
        ));
    }
}