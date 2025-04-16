<?php

namespace App\Command;

use App\Service\TaskAutoDeleteService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:schedule-task-deletion',
    description: 'Schedule deletion of old tasks based on configuration',
)]
class ScheduleTaskDeletionCommand extends Command
{
    private TaskAutoDeleteService $taskAutoDeleteService;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        TaskAutoDeleteService $taskAutoDeleteService,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
        $this->taskAutoDeleteService = $taskAutoDeleteService;
        $this->parameterBag = $parameterBag;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $isEnabled = $this->parameterBag->get('app.task_auto_delete.enabled');
        $days = $this->parameterBag->get('app.task_auto_delete.days');

        if (!$isEnabled) {
            $io->note('Task auto-deletion is disabled. Enable it by setting TASK_AUTO_DELETE_ENABLED=true');
            return Command::SUCCESS;
        }

        $io->info(sprintf('Starting task auto-deletion for tasks older than %d days', $days));
        
        $this->taskAutoDeleteService->scheduleTasksDeletion();
        
        $io->success('Task deletion has been scheduled.');

        return Command::SUCCESS;
    }
}