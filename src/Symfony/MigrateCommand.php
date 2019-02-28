<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration\Symfony;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\Service\MigrateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrateService->migrate($this->defaultContext);
    }
}
