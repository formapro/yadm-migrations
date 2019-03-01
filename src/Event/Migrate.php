<?php declare(strict_types=1);

namespace Formapro\Yadm\Migration\Event;

use Formapro\Yadm\Migration\Migration;
use Formapro\Yadm\Migration\MigrationFile;
use Symfony\Component\EventDispatcher\Event;

class Migrate extends Event
{
    /**
     * @var MigrationFile
     */
    private $migrationFile;

    /**
     * @var Migration
     */
    private $migration;

    public function __construct(MigrationFile $migrationFile, Migration $migration)
    {
        $this->migrationFile = $migrationFile;
        $this->migration = $migration;
    }

    public function getMigrationFile(): MigrationFile
    {
        return $this->migrationFile;
    }

    public function getMigration(): Migration
    {
        return $this->migration;
    }
}
