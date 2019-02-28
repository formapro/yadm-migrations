<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration\Service;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\MigrationFile;

class GenerateService
{
    public function generate(Context $context): MigrationFile
    {
        if (false === is_file($context->getTemplateFile()) || false === is_readable($context->getTemplateFile())) {
            throw new \LogicException(sprintf('Migration template file is missing or not readable: "%s"', $context->getTemplateFile()));
        }

        if (false === file_exists($context->getDir())) {
            if (false === $context->isCreateDirIfNotExists()) {
                throw new \LogicException(sprintf('Migrations directory does not exist: "%s"', $context->getDir()));
            }

            if (false === mkdir($context->getDir(), 0777, true)) {
                throw new \LogicException(sprintf('Could not create directory: "%s"', $context->getDir()));
            }
        }

        $version = (new \DateTime('now', new \DateTimeZone('UTC')))->format('YmdHis');
        $class = $context->getClassPrefix().$version;
        $path = sprintf('%s/%s.php', $context->getDir(), $class);

        $params = [
            '{namespace}' => $context->getNamespace(),
            '{class}' => $class,
        ];

        $template = file_get_contents($context->getTemplateFile());
        $template = str_replace(array_keys($params), array_values($params), $template);

        file_put_contents($path, $template);

        $file = new MigrationFile();
        $file->setFilePath($path);
        $file->setClass($class);
        $file->setVersion($version);

        return $file;
    }
}
