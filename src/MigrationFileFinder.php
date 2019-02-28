<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;

interface MigrationFileFinder
{
    /**
     * @param Context $context
     *
     * @return MigrationFile[]
     */
    public function find(Context $context): array;
}
