<?php

namespace Formapro\Yadm\Migration\Tests\Service;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\ExecutedMigrationsStorage;
use Formapro\Yadm\Migration\Migration;
use Formapro\Yadm\Migration\MigrationFactory;
use Formapro\Yadm\Migration\MigrationFile;
use Formapro\Yadm\Migration\MigrationFileFinder;
use Formapro\Yadm\Migration\Service\MigrateService;
use Formapro\Yadm\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MigrateServiceTest extends TestCase
{
    public function testShouldMigrate()
    {
        $file1 = new MigrationFile();
        $file1->setVersion('1');

        $file2 = new MigrationFile();
        $file2->setVersion('2');

        $file3 = new MigrationFile();
        $file3->setVersion('3');

        $migrationFiles = [$file1, $file3, $file2];

        $fileFinder = $this->createMock(MigrationFileFinder::class);
        $fileFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn($migrationFiles)
        ;

        $migration2 = $this->createMock(Migration::class);
        $migration2
            ->expects($this->once())
            ->method('execute')
        ;

        $migration3 = $this->createMock(Migration::class);
        $migration3
            ->expects($this->once())
            ->method('execute')
        ;

        $migrationFactory = $this->createMock(MigrationFactory::class);
        $migrationFactory
            ->expects($this->at(0))
            ->method('create')
            ->willReturn($migration2)
        ;
        $migrationFactory
            ->expects($this->at(1))
            ->method('create')
            ->willReturn($migration3)
        ;

        $executedMigrationStorage = $this->createMock(ExecutedMigrationsStorage::class);
        $executedMigrationStorage
            ->expects($this->once())
            ->method('getVersions')
            ->willReturn(['1'])
        ;
        $executedMigrationStorage
            ->expects($this->at(1))
            ->method('pushVersion')
            ->with('2')
        ;
        $executedMigrationStorage
            ->expects($this->at(2))
            ->method('pushVersion')
            ->with('3')
        ;

        $yadm = $this->createMock(Registry::class);

        $migrateService = new MigrateService($fileFinder, $executedMigrationStorage, $migrationFactory, $yadm);
        $migrateService->migrate(new Context(), new EventDispatcher());
    }
}
