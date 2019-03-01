<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration\Symfony;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\Service\GenerateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected static $defaultName = 'yadm:migrations:generate';

    /**
     * @var Context
     */
    private $defaultContext;

    /**
     * @var GenerateService
     */
    private $generateService;

    public function __construct(Context $defaultContext, GenerateService $generateService)
    {
        parent::__construct();

        $this->defaultContext = $defaultContext;
        $this->generateService = $generateService;
    }

    protected function configure()
    {
        $this->setDescription('Generate a blank migration class');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $this->generateService->generate($this->defaultContext);

        $output->writeln(sprintf('Generated new migration class to "<info>%s</info>"', $file->getFilePath()));
    }
}
