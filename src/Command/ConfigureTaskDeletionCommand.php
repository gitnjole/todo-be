<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:configure-task-deletion',
    description: 'Configure task auto-deletion settings',
)]
class ConfigureTaskDeletionCommand extends Command
{
    private ParameterBagInterface $parameterBag;
    private string $projectDir;

    public function __construct(
        ParameterBagInterface $parameterBag,
        string $projectDir
    ) {
        parent::__construct();
        $this->parameterBag = $parameterBag;
        $this->projectDir = $projectDir;
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'enable',
                null,
                InputOption::VALUE_NONE,
                'Enable task auto-deletion'
            )
            ->addOption(
                'disable',
                null,
                InputOption::VALUE_NONE,
                'Disable task auto-deletion'
            )
            ->addOption(
                'days',
                null,
                InputOption::VALUE_REQUIRED,
                'Number of days after which tasks should be deleted'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileSystem = new Filesystem();
        $envFile = "{$this->projectDir}/.env";
        
        if (!$fileSystem->exists($envFile)) {
            $fileSystem->touch($envFile);
        }
        
        $dotEnv = new Dotenv();
        $dotEnv->load($envFile);
        
        $enabled = $_ENV['TASK_AUTO_DELETE_ENABLED'] ?? $this->parameterBag->get('app.task_auto_delete.enabled');
        $days = $_ENV['TASK_AUTO_DELETE_DAYS'] ?? $this->parameterBag->get('app.task_auto_delete.days');
        
        if ($input->getOption('enable')) {
            $enabled = 'true';
            $io->info('Task auto-deletion has been enabled');
        } elseif ($input->getOption('disable')) {
            $enabled = 'false';
            $io->info('Task auto-deletion has been disabled');
        }
        
        if ($input->getOption('days')) {
            $days = (int) $input->getOption('days');
            if ($days <= 0) {
                $io->error('Days must be a positive number');
                return Command::FAILURE;
            }
            $io->info(sprintf('Task auto-deletion period set to %d days', $days));
        }
        
        $envContent = file_get_contents($envFile);
        
        if (preg_match('/^TASK_AUTO_DELETE_ENABLED=.*$/m', $envContent)) {
            $envContent = preg_replace(
                '/^TASK_AUTO_DELETE_ENABLED=.*$/m',
                "TASK_AUTO_DELETE_ENABLED={$enabled}",
                $envContent
            );
        } else {
            $envContent .= "\nTASK_AUTO_DELETE_ENABLED={$enabled}";
        }
        
        if (preg_match('/^TASK_AUTO_DELETE_DAYS=.*$/m', $envContent)) {
            $envContent = preg_replace(
                '/^TASK_AUTO_DELETE_DAYS=.*$/m',
                "TASK_AUTO_DELETE_DAYS={$days}",
                $envContent
            );
        } else {
            $envContent .= "\nTASK_AUTO_DELETE_DAYS={$days}";
        }
        
        file_put_contents($envFile, $envContent);
        
        $io->section('Current Task Auto-Deletion Configuration');
        $io->table(
            ['Setting', 'Value'],
            [
                ['Enabled', $enabled],
                ['Delete After (days)', $days],
            ]
        );
        
        $io->success('Configuration updated successfully');
        
        return Command::SUCCESS;
    }
}