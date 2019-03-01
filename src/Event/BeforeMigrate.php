<?php declare(strict_types=1);

namespace Formapro\Yadm\Migration\Event;

use Formapro\Yadm\Migration\MigrationFile;
use Symfony\Component\EventDispatcher\Event;

class BeforeMigrate extends Event
{
    /**
     * @var bool
     */
    private $canceled;

    /**
     * @var MigrationFile[]
     */
    private $migrationFiles;

    /**
     * @var string[]
     */
    private $missingMigrationFiles;

    public function __construct(array $migrationFiles, array $missingMigrationFiles)
    {
        $this->migrationFiles = $migrationFiles;
        $this->canceled = false;
        $this->missingMigrationFiles = $missingMigrationFiles;
    }

    /**
     * @return MigrationFile[]
     */
    public function getMigrationFiles(): array
    {
        return $this->migrationFiles;
    }

    /**
     * @return string[]
     */
    public function getMissingMigrationFiles(): array
    {
        return $this->missingMigrationFiles;
    }

    public function isCanceled(): bool
    {
        return $this->canceled;
    }

    public function setCanceled(bool $canceled)
    {
        $this->canceled = $canceled;
    }
}
