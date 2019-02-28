<?php

namespace Formapro\Yadm\Migration\Symfony;

use Formapro\Yadm\Migration\Migration;
use Formapro\Yadm\Migration\MigrationFactory;
use Formapro\Yadm\Migration\MigrationFile;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyMigrationFactory implements MigrationFactory
{
    /**
     * @var MigrationFactory
     */
    private $parentFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(MigrationFactory $parentFactory, ContainerInterface $container)
    {
        $this->parentFactory = $parentFactory;
        $this->container = $container;
    }

    public function create(MigrationFile $file): Migration
    {
        $migration = $this->parentFactory->create($file);

        if ($migration instanceof ContainerAwareInterface) {
            $migration->setContainer($this->container);
        }

        return $migration;
    }
}
