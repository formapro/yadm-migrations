<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;


class MigrationFile
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $filePath;

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class)
    {
        $this->class = $class;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;
    }
}
