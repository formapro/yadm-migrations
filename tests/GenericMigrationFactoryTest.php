<?php

namespace Formapro\Yadm\Migration\Tests;

use Formapro\Yadm\Migration\GenericMigrationFactory;
use Formapro\Yadm\Migration\Migration;
use Formapro\Yadm\Migration\MigrationFile;
use PHPUnit\Framework\TestCase;

class GenericMigrationFactoryTest extends TestCase
{
    public function testShouldThrowIfMigrationFileDoesNotExist()
    {
        $file = new MigrationFile();
        $file->setFilePath(__DIR__.'/file-does-not-exist');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Migration file does not exist');

        $factory = new GenericMigrationFactory();
        $factory->create($file);
    }

    public function testShouldThrowIfMigrationClassDoesNotExist()
    {
        $tmpDir = sys_get_temp_dir().'/'.uniqid('yadm_');
        mkdir($tmpDir);

        $filePath = $tmpDir.'/Migration123.php';

        touch($filePath);

        $file = new MigrationFile();
        $file->setFilePath($filePath);
        $file->setClass('Migration123');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Migration class does not exist');

        $factory = new GenericMigrationFactory();
        $factory->create($file);
    }

    public function testShouldThrowIfMigrationClassDoesNotExtendMigration()
    {
        $tmpDir = sys_get_temp_dir().'/'.uniqid('yadm_');
        mkdir($tmpDir);

        $content = <<<EOL
<?php

namespace App\Migrations;

class {class} {
}
EOL;

        $class = 'Migration'.uniqid();
        $filePath = $tmpDir.'/'.$class.'.php';
        $content = str_replace('{class}', $class, $content);

        file_put_contents($filePath, $content);

        $file = new MigrationFile();
        $file->setFilePath($filePath);
        $file->setClass('App\\Migrations\\'.$class);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid migration file loaded, expected instance of: "Formapro\Yadm\Migration\Migration"');

        $factory = new GenericMigrationFactory();
        $factory->create($file);
    }

    public function testShouldCreateMigrationClass()
    {
        $tmpDir = sys_get_temp_dir().'/'.uniqid('yadm_');
        mkdir($tmpDir);

        $content = <<<EOL
<?php

namespace App\Migrations;

use Formapro\Yadm\Migration\Migration;

class {class} implements Migration {

    public function execute() {}
}
EOL;

        $class = 'Migration'.uniqid();
        $filePath = $tmpDir.'/'.$class.'.php';
        $content = str_replace('{class}', $class, $content);
        file_put_contents($filePath, $content);

        $file = new MigrationFile();
        $file->setFilePath($filePath);
        $file->setClass('App\\Migrations\\'.$class);

        $factory = new GenericMigrationFactory();
        $migration = $factory->create($file);

        $this->assertInstanceOf(Migration::class, $migration);
    }
}
