<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;

use Formapro\Yadm\Registry;

interface Migration
{
    public function execute(Registry $yadm): void;
}
