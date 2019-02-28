<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;

interface MigrationFactory
{
    public function create(MigrationFile $file): Migration;
}
