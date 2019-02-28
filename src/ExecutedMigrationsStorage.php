<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;

interface ExecutedMigrationsStorage
{
    /**
     * @return string[]
     */
    public function getVersions(): array;

    public function pushVersion(string $version): void;
}
