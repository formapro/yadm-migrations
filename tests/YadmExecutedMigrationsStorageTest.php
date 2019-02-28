<?php

namespace Formapro\Yadm\Migration\Tests;

use Formapro\Yadm\Hydrator;
use Formapro\Yadm\Migration\YadmExecutedMigrationsStorage;
use Formapro\Yadm\Storage;

class YadmExecutedMigrationsStorageTest extends FunctionalTest
{
    public function test_CouldAddAndGetExecutedVersions()
    {
        $executedMigrationsStorage = new YadmExecutedMigrationsStorage($this->getStorage());

        $executedMigrationsStorage->pushVersion('123');
        $executedMigrationsStorage->pushVersion('321');

        $versions = $executedMigrationsStorage->getVersions();

        $this->assertCount(2, $versions);

        $this->assertSame('123', $versions[0]);
        $this->assertSame('321', $versions[1]);
    }

    private function getStorage(): Storage
    {
        $collection = $this->database->selectCollection('storage_test');
        $hydrator = new Hydrator(Model::class);

        return new Storage($collection, $hydrator);
    }
}

class Model {

}
