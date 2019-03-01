<?php

namespace Formapro\Yadm\Migration\Tests;

use Formapro\Yadm\Migration\YadmExecutedMigrationsStorage;

class YadmExecutedMigrationsStorageTest extends FunctionalTest
{
    public function test_CouldAddAndGetExecutedVersions()
    {
        $executedMigrationsStorage = new YadmExecutedMigrationsStorage($this->database->selectCollection('storage_test'));

        $executedMigrationsStorage->pushVersion('123');
        $executedMigrationsStorage->pushVersion('321');

        $versions = $executedMigrationsStorage->getVersions();

        $this->assertCount(2, $versions);

        $this->assertSame('123', $versions[0]);
        $this->assertSame('321', $versions[1]);
    }
}
