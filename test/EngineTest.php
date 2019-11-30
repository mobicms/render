<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace MobicmsTest;

use Mobicms\Render\Engine;
use Mobicms\Render\Template\Folder;
use Mobicms\Render\Template\TemplateFunction;
use LogicException;
use MobicmsTest\Extension\DummyExtensionFoo;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    /** @var Engine */
    private $engine;

    public function setUp(): void
    {
        vfsStream::setup('templates');
        $this->engine = new Engine();
    }

    public function testCanCreateInstance(): void
    {
        $this->assertInstanceOf(Engine::class, $this->engine);
    }

    public function testSetFileExtension(): void
    {
        $this->assertInstanceOf(Engine::class, $this->engine->setFileExtension('tpl'));
        $this->assertEquals($this->engine->getFileExtension(), 'tpl');
    }

    public function testGetFileExtension(): void
    {
        $this->assertEquals($this->engine->getFileExtension(), 'php');
    }

    public function testAddFolder(): void
    {
        vfsStream::create(['folder' => ['template.php' => '']]);
        $this->assertInstanceOf(Engine::class, $this->engine->addFolder('folder', vfsStream::url('templates/folder')));
        $this->assertEquals($this->engine->getFolder('folder')['directory'], 'vfs://templates/folder');
    }

    public function testAddFolderWithNamespaceConflict(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template namespace "name" is already being used.');
        $this->engine->addFolder('name', vfsStream::url('templates'));
        $this->engine->addFolder('name', vfsStream::url('templates'));
    }

    public function testAddFolderWithInvalidDirectory(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The specified directory path "vfs://does/not/exist" does not exist.');
        $this->engine->addFolder('namespace', vfsStream::url('does/not/exist'));
    }

    public function testGetFolder(): void
    {
        $this->engine->addFolder('name', vfsStream::url('templates'));
        $folder = $this->engine->getFolder('name');
        $this->assertIsArray($folder);
        $this->assertArrayHasKey('name', $folder);
        $this->assertArrayHasKey('directory', $folder);
    }

    public function testGetNonexistentFolder(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template namespace "name" was not found.');
        $this->engine->getFolder('name');
    }

    public function testAddData(): void
    {
        $this->engine->addData(['name' => 'Jonathan']);
        $data = $this->engine->getData();
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataWithTemplate(): void
    {
        $this->engine->addData(['name' => 'Jonathan'], 'template');
        $data = $this->engine->getData('template');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataWithTemplates(): void
    {
        $this->engine->addData(['name' => 'Jonathan'], ['template1', 'template2']);
        $data = $this->engine->getData('template1');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testRegisterFunction(): void
    {
        vfsStream::create(['template.php' => '<?=$this->uppercase($name)?>']);
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertInstanceOf(TemplateFunction::class, $this->engine->getFunction('uppercase'));
        $this->assertEquals($this->engine->getFunction('uppercase')->getCallback(), 'strtoupper');
    }

    public function testRegisterExistFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function name "uppercase" is already registered.');
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->engine->registerFunction('uppercase', 'strtoupper');
    }

    public function testGetFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $function = $this->engine->getFunction('uppercase');
        $this->assertInstanceOf(TemplateFunction::class, $function);
        $this->assertEquals($function->getName(), 'uppercase');
        $this->assertEquals($function->getCallback(), 'strtoupper');
    }

    public function testGetInvalidFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function "some_function_that_does_not_exist" was not found.');
        $this->engine->getFunction('some_function_that_does_not_exist');
    }

    public function testDoesFunctionExist(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertTrue($this->engine->doesFunctionExist('uppercase'));
    }

    public function testDoesFunctionNotExist(): void
    {
        $this->assertFalse($this->engine->doesFunctionExist('some_function_that_does_not_exist'));
    }

    public function testLoadExtension()
    {
        $this->assertFalse($this->engine->doesFunctionExist('foo'));
        $this->engine->loadExtension(new DummyExtensionFoo());
        $this->assertTrue($this->engine->doesFunctionExist('foo'));
    }

    public function testRenderTemplate()
    {
        $this->engine->addFolder('tmp', vfsStream::url('templates'));
        vfsStream::create(['template.php' => 'Hello!']);
        $this->assertEquals($this->engine->render('tmp::template'), 'Hello!');
    }
}
