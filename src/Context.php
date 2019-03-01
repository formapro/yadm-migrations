<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;

class Context
{
    /**
     * Dir where migrations lives
     *
     * @var string
     */
    private $dir;

    /**
     * @var bool
     */
    private $createDirIfNotExists;

    /**
     * Migration file class prefix
     *
     * @var string
     */
    private $classPrefix;

    /**
     * Migrations namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * Path to migration template file
     *
     * @var string
     */
    private $templateFile;

    public function __construct(array $config = [])
    {
        if (isset($config['dir'])) {
            $this->setDir($config['dir']);
        }

        if (isset($config['createDirIfNotExists'])) {
            $this->setCreateDirIfNotExists($config['createDirIfNotExists']);
        }

        if (isset($config['classPrefix'])) {
            $this->setClassPrefix($config['classPrefix']);
        }

        if (isset($config['namespace'])) {
            $this->setNamespace($config['namespace']);
        }

        if (isset($config['templateFile'])) {
            $this->setTemplateFile($config['templateFile']);
        }
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function setDir(string $dir)
    {
        $this->dir = $dir;
    }

    public function isCreateDirIfNotExists(): bool
    {
        return $this->createDirIfNotExists;
    }

    public function setCreateDirIfNotExists(bool $createDirIfNotExists)
    {
        $this->createDirIfNotExists = $createDirIfNotExists;
    }

    public function getClassPrefix(): string
    {
        return $this->classPrefix;
    }

    public function setClassPrefix(string $classPrefix)
    {
        $this->classPrefix = $classPrefix;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function getTemplateFile(): string
    {
        return $this->templateFile;
    }

    public function setTemplateFile(string $templateFile)
    {
        $this->templateFile = $templateFile;
    }
}
