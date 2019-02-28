<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;

class GenericMigrationFactory implements MigrationFactory
{
    public function create(MigrationFile $file): Migration
    {
        if (false === file_exists($file->getFilePath())) {
            throw new \LogicException(sprintf('Migration file does not exist: "%s"', $file->getFilePath()));
        }

        require_once $file->getFilePath();

        if (false === class_exists($file->getClass())) {
            throw new \LogicException(sprintf('Migration class does not exist: "%s"', $file->getClass()));
        }

        $class = $file->getClass();

        $migration = new $class;

        if (false === $migration instanceof Migration) {
            throw new \LogicException(sprintf('Invalid migration file loaded, expected instance of: "%s"', Migration::class));
        }

        return $migration;
    }
}
