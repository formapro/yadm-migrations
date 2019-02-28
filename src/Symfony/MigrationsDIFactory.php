<?php

namespace Formapro\Yadm\Migration\Symfony;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\ExecutedMigrationsStorage;
use Formapro\Yadm\Migration\GenericMigrationFactory;
use Formapro\Yadm\Migration\GenericMigrationFileFinder;
use Formapro\Yadm\Migration\MigrationFactory;
use Formapro\Yadm\Migration\MigrationFileFinder;
use Formapro\Yadm\Migration\Service\GenerateService;
use Formapro\Yadm\Migration\Service\MigrateService;
use Formapro\Yadm\Migration\YadmExecutedMigrationsStorage;
use Formapro\Yadm\Registry;
use MongoDB\Collection;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MigrationsDIFactory
{
    public static function getConfiguration(string $name = 'migrations'): NodeDefinition
    {
        $builder = new ArrayNodeDefinition($name);
        $builder
            ->canBeEnabled()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('dir')->defaultValue('%kernel.project_dir%/YadmMigrations')->cannotBeEmpty()->end()
                ->booleanNode('createDirIfNotExists')->defaultValue(true)->end()
                ->scalarNode('classPrefix')->defaultValue('Migration')->cannotBeEmpty()->end()
                ->scalarNode('namespace')->defaultValue('App\\YadmMigrations')->cannotBeEmpty()->end()
                ->scalarNode('templateFile')->defaultValue(__DIR__ . '/../../Migration.php.tmpl')->cannotBeEmpty()->end()
                ->scalarNode('database')->defaultNull()->end()
                ->scalarNode('collection')->defaultValue('migrations')->cannotBeEmpty()->end()
            ->end()
        ;

        return $builder;
    }

    public static function buildServices(array $config, ContainerBuilder $container): void
    {
        $container->register(Context::class)
            ->addArgument($config)
        ;

        $container->register(GenericMigrationFileFinder::class);
        $container->setAlias(MigrationFileFinder::class, GenericMigrationFileFinder::class);

        $container->register(GenericMigrationFactory::class);

        $container->register(SymfonyMigrationFactory::class)
            ->addArgument(new Reference(GenericMigrationFactory::class))
            ->addArgument(new Reference('service_container'))
        ;

        $container->setAlias(MigrationFactory::class, SymfonyMigrationFactory::class);

        $container->register('yadm.migrations.collection', Collection::class)
            ->setFactory([new Reference('yadm.collection_factory'), 'create'])
            ->addArgument($config['collection'])
            ->addArgument($config['database'])
        ;

        $container->register(YadmExecutedMigrationsStorage::class)
            ->addArgument(new Reference('yadm.migrations.collection'))
        ;
        $container->setAlias(ExecutedMigrationsStorage::class, YadmExecutedMigrationsStorage::class);

        $container->register(MigrateService::class)
            ->addArgument(new Reference(MigrationFileFinder::class))
            ->addArgument(new Reference(ExecutedMigrationsStorage::class))
            ->addArgument(new Reference(MigrationFactory::class))
            ->addArgument(new Reference(Registry::class))
        ;

        $container->register(GenerateService::class);

        $container->register(MigrateCommand::class)
            ->addArgument(new Reference(Context::class))
            ->addArgument(new Reference(MigrateService::class))
            ->addTag('console.command')
        ;

        $container->register(GenerateCommand::class)
            ->addArgument(new Reference(Context::class))
            ->addArgument(new Reference(GenerateService::class))
            ->addTag('console.command')
        ;
    }
}