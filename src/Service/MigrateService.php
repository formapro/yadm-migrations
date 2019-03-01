<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration\Service;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\Event\AfterMigrate;
use Formapro\Yadm\Migration\Event\BeforeMigrate;
use Formapro\Yadm\Migration\Event\Migrate;
use Formapro\Yadm\Migration\ExecutedMigrationsStorage;
use Formapro\Yadm\Migration\MigrationFactory;
use Formapro\Yadm\Migration\MigrationFile;
use Formapro\Yadm\Migration\MigrationFileFinder;
use Formapro\Yadm\Registry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MigrateService
{
    /**
     * @var MigrationFileFinder
     */
    private $migrationFileFinder;

    /**
     * @var ExecutedMigrationsStorage
     */
    private $executedMigrationsStorage;

    /**
     * @var MigrationFactory
     */
    private $migrationFactory;

    /**
     * @var Registry
     */
    private $yadm;

    public function __construct(
        MigrationFileFinder $migrationFileFinder,
        ExecutedMigrationsStorage $executedMigrationsStorage,
        MigrationFactory $migrationFactory,
        Registry $yadm
    ) {
        $this->migrationFileFinder = $migrationFileFinder;
        $this->executedMigrationsStorage = $executedMigrationsStorage;
        $this->migrationFactory = $migrationFactory;
        $this->yadm = $yadm;
    }

    public function migrate(Context $context, EventDispatcherInterface $eventDispatcher): void
    {
        $migrationFiles = $this->migrationFileFinder->find($context);
        $versions = $this->executedMigrationsStorage->getVersions();

        $availableVersions = [];
        foreach ($migrationFiles as $migrationFile) {
            $availableVersions[] = $migrationFile->getVersion();
        }

        $missingMigrationFiles = array_diff($versions, $availableVersions);
        $migrationsToExecute = array_diff($availableVersions, $versions);

        $migrationFiles = array_filter($migrationFiles, function(MigrationFile $file) use ($migrationsToExecute) {
            return in_array($file->getVersion(), $migrationsToExecute, true);
        });

        usort($migrationFiles, function (MigrationFile $a, MigrationFile $b) {
            return $a->getVersion() <=> $b->getVersion();
        });

        $eventDispatcher->dispatch(BeforeMigrate::class, $event = new BeforeMigrate($migrationFiles, $missingMigrationFiles));

        if ($event->isCanceled()) {
            return;
        }

        $startAt = time();

        foreach ($migrationFiles as $migrationFile) {
            $migration = $this->migrationFactory->create($migrationFile);

            $eventDispatcher->dispatch(Migrate::class, new Migrate($migrationFile, $migration));

            $migration->execute($this->yadm);

            $this->executedMigrationsStorage->pushVersion($migrationFile->getVersion());
        }

        $eventDispatcher->dispatch(AfterMigrate::class, new AfterMigrate(time() - $startAt, count($migrationFiles)));
    }
}
