<?php declare(strict_types=1);

namespace Formapro\Yadm\Migration\Event;

use Symfony\Component\EventDispatcher\Event;

class AfterMigrate extends Event
{
    /**
     * @var int
     */
    private $executionTime;

    /**
     * @var int
     */
    private $executedCount;

    public function __construct(int $executionTime, int $executedCount)
    {
        $this->executionTime = $executionTime;
        $this->executedCount = $executedCount;
    }

    public function getExecutionTime(): int
    {
        return $this->executionTime;
    }

    public function getExecutedCount(): int
    {
        return $this->executedCount;
    }
}
