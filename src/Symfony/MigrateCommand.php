<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration\Symfony;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\Event\AfterMigrate;
use Formapro\Yadm\Migration\Event\BeforeMigrate;
use Formapro\Yadm\Migration\Event\Migrate;
use Formapro\Yadm\Migration\Service\MigrateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MigrateCommand extends Command
{
    protected static $defaultName = 'yadm:migrations:migrate';

    /**
     * @var Context
     */
    private $defaultContext;

    /**
     * @var MigrateService
     */
    private $migrateService;

    public function __construct(Context $defaultContext, MigrateService $migrateService)
    {
        parent::__construct();

        $this->defaultContext = $defaultContext;
        $this->migrateService = $migrateService;
    }

    protected function configure()
    {
        $this->setDescription('Execute a database migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->addListener(BeforeMigrate::class, function (BeforeMigrate $event) use ($input, $output) {
            if ($event->getMissingMigrationFiles()) {
                $output->writeln(sprintf('<error>Executed before but migration files are missing now:</error> <comment>%s</comment>', implode(', ', $event->getMissingMigrationFiles())));
            }

            if (empty($event->getMigrationFiles())) {
                $event->setCanceled(true);
                $output->writeln('There are no migrations to execute');
                return;
            }

            $versions = [];
            foreach ($event->getMigrationFiles() as $file) {
                $versions[] = $file->getVersion();
            }

            $output->writeln(sprintf('Next migrations will be executed: <comment>%s</comment>', implode(', ', $versions)));

            if ($input->isInteractive()) {
                $question = 'WARNING! You are about to execute a database migration'
                    . ' that could result in schema changes and data loss.'
                    . ' Are you sure you wish to continue? (y/n)';

                if (false === $this->getHelper('question')->ask($input, $output, new ConfirmationQuestion($question))) {
                    $event->setCanceled(true);
                    $output->writeln('<error>Migration cancelled!</error>');

                    return;
                }
            }
        });

        $eventDispatcher->addListener(Migrate::class, function (Migrate $event) use ($output) {
            $output->writeln(sprintf('Execute migration: <comment>%s</comment>', $event->getMigrationFile()->getVersion()));
        });

        $eventDispatcher->addListener(AfterMigrate::class, function (AfterMigrate $event) use ($output) {
            $output->writeln(PHP_EOL.'  <comment>------------------------</comment>');
            $output->writeln(sprintf('  <info>++</info> finished in %ss', $event->getExecutionTime()));
            $output->writeln(sprintf('  <info>++</info> %s migrations executed', $event->getExecutedCount()));
        });

        $this->migrateService->migrate($this->defaultContext, $eventDispatcher);
    }
}
