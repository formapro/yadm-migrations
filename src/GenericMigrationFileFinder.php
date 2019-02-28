<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;

class GenericMigrationFileFinder implements MigrationFileFinder
{
    public function find(Context $context): array
    {
        if (false === is_dir($context->getDir()) || false === is_executable($context->getDir())) {
            throw new \InvalidArgumentException(sprintf('Directory does not exist or not readable: "%s"', $context->getDir()));
        }

        $filePattern = sprintf('#^.+\\%s%s[^\\%s]{1,255}\\.php$#i', DIRECTORY_SEPARATOR, $context->getClassPrefix(), DIRECTORY_SEPARATOR);

        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($context->getDir(), \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            ),
            $filePattern,
            \RegexIterator::GET_MATCH
        );

        $files = [];
        foreach ($iterator as $item) {
            $files[] = $file = new MigrationFile();
            $class = basename($item[0], '.php');

            $file->setClass(sprintf('%s\\%s', $context->getNamespace(), $class));
            $file->setFilePath($item[0]);
            $file->setVersion(substr($class, strlen($context->getClassPrefix())));
        }

        return $files;
    }
}
