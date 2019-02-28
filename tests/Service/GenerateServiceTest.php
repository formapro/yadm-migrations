<?php

namespace Formapro\Yadm\Migration\Tests\Service;

use Formapro\Yadm\Migration\Context;
use Formapro\Yadm\Migration\Service\GenerateService;
use PHPUnit\Framework\TestCase;

class GenerateServiceTest extends TestCase
{
    public function testShouldThrowIfTemplateFileDoesNotExist()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Migration template file is missing or not readable');

        $context = new Context();
        $context->setTemplateFile(__DIR__.'/file-does-not-exist');

        $generateService = new GenerateService();
        $generateService->generate($context);
    }

    public function testShouldThrowIfCreateDirIfNotExistsIsFalse()
    {
        $tmpDir = sys_get_temp_dir().'/'.uniqid('yadm_');
        mkdir($tmpDir);

        $dir = $tmpDir.'/migrations';
        $template = $tmpDir.'/template.php';

        touch($template);

        $context = new Context();
        $context->setDir($dir);
        $context->setTemplateFile($template);
        $context->setCreateDirIfNotExists(false);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Migrations directory does not exist');

        $generateService = new GenerateService();
        $generateService->generate($context);
    }

    public function testShouldGenerateMigration()
    {
        $tmpDir = sys_get_temp_dir().'/'.uniqid('yadm_');
        mkdir($tmpDir);

        $dir = $tmpDir.'/migrations';
        $template = $tmpDir.'/template.php';

        $content = <<<EOL
{namespace}
{class}
EOL;

        file_put_contents($template, $content);

        $context = new Context();
        $context->setDir($dir);
        $context->setTemplateFile($template);
        $context->setCreateDirIfNotExists(true);
        $context->setClassPrefix('TheClass');
        $context->setNamespace('The\Namespace');

        $generateService = new GenerateService();
        $file = $generateService->generate($context);

        $expectedFile = str_replace(['{namespace}', '{class}'], [$context->getNamespace(), $context->getClassPrefix().$file->getVersion()], $content);

        $this->assertNotEmpty($file->getVersion());
        $this->assertSame($expectedFile, file_get_contents($file->getFilePath()));
        $this->assertSame($dir.'/'.$file->getClass().'.php', $file->getFilePath());
        $this->assertSame($context->getClassPrefix().$file->getVersion(), $file->getClass());
    }
}
