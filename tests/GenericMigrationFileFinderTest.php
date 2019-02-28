<?php

namespace Formapro\Yadm\Migration\Tests;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\GenericMigrationFileFinder;
use PHPUnit\Framework\TestCase;

class GenericMigrationFileFinderTest extends TestCase
{
    public function testShouldThrowIfMigrationsDirDoesNotExist()
    {
        $context = new Context();
        $context->setDir(__DIR__.'/dir-does-not-exist');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Directory does not exist or not readable');

        $finder = new GenericMigrationFileFinder();
        $finder->find($context);
    }

    public function testShouldFindVersionFiles()
    {
        $tmpDir = sys_get_temp_dir().'/'.uniqid('yadm_');
        mkdir($tmpDir);
        touch($tmpDir.'/Migration123.php');
        touch($tmpDir.'/Migration321.php');
        touch($tmpDir.'/NotMigrationFile.php');

        $context = new Context();
        $context->setDir($tmpDir);
        $context->setClassPrefix('Migration');
        $context->setNamespace('App\\Migrations');

        $finder = new GenericMigrationFileFinder();
        $files = $finder->find($context);

        $this->assertCount(2, $files);

        $this->assertSame('App\Migrations\Migration123', $files[0]->getClass());
        $this->assertSame('123', $files[0]->getVersion());

        $this->assertSame('App\Migrations\Migration321', $files[1]->getClass());
        $this->assertSame('321', $files[1]->getVersion());
    }
}
