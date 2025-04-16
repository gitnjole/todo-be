<?php

namespace App\Service;

use App\Message\TaskDeletionMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class TaskAutoDeleteService
{
    private MessageBusInterface $messageBus;
    private ParameterBagInterface $parameterBag;
    private LoggerInterface $logger;

    public function __construct(
        MessageBusInterface $messageBus,
        ParameterBagInterface $parameterBag,
        LoggerInterface $logger
    ) {
        $this->messageBus = $messageBus;
        $this->parameterBag = $parameterBag;
        $this->logger = $logger;
    }

    public function scheduleTasksDeletion(): void
    {
        $isEnabled = $this->parameterBag->get('app.task_auto_delete.enabled');
        
        if (!$isEnabled) {
            $this->logger->info('Task auto-deletion is disabled');
            return;
        }
        
        $days = (int) $this->parameterBag->get('app.task_auto_delete.days');
        
        if ($days <= 0) {
            $this->logger->warning('Invalid task auto-deletion days setting: ' . $days);
            return;
        }
        
        $olderThan = new \DateTimeImmutable("-{$days} days");
        
        $this->messageBus->dispatch(new TaskDeletionMessage($olderThan));
        
        $this->logger->info(sprintf(
            'Scheduled deletion of tasks older than %d days (%s)',
            $days,
            $olderThan->format('Y-m-d')
        ));
    }
}