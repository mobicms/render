<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest;

use League\Plates\Engine;
use League\Plates\Template\Folders;
use League\Plates\Template\Func;
use League\Plates\Template\Template;
use LogicException;
use MobicmsTest\Extension\DummyExtensionBar;
use MobicmsTest\Extension\DummyExtensionFoo;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    private $engine;

    public function setUp() : void
    {
        vfsStream::setup('templates');
        $this->engine = new Engine(vfsStream::url('templates'));
    }

    public function testCanCreateInstance() : void
    {
        $this->assertInstanceOf(Engine::class, $this->engine);
    }

    public function testSetDirectory() : void
    {
        $this->assertInstanceOf(Engine::class, $this->engine->setDirectory(vfsStream::url('templates')));
        $this->assertEquals($this->engine->getDirectory(), vfsStream::url('templates'));
    }

    public function testSetNullDirectory() : void
    {
        $this->assertInstanceOf(Engine::class, $this->engine->setDirectory(null));
        $this->assertNull($this->engine->getDirectory());
    }

    public function testSetInvalidDirectory() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The specified path "vfs://does/not/exist" does not exist.');
        $this->engine->setDirectory(vfsStream::url('does/not/exist'));
    }

    public function testGetDirectory() : void
    {
        $this->assertEquals($this->engine->getDirectory(), vfsStream::url('templates'));
    }

    public function testSetFileExtension() : void
    {
        $this->assertInstanceOf(Engine::class, $this->engine->setFileExtension('tpl'));
        $this->assertEquals($this->engine->getFileExtension(), 'tpl');
    }

    public function testSetNullFileExtension() : void
    {
        $this->assertInstanceOf(Engine::class, $this->engine->setFileExtension(null));
        $this->assertNull($this->engine->getFileExtension());
    }

    public function testGetFileExtension() : void
    {
        $this->assertEquals($this->engine->getFileExtension(), 'php');
    }

    public function testAddFolder() : void
    {
        vfsStream::create(['folder' => ['template.php' => '']]);
        $this->assertInstanceOf(Engine::class, $this->engine->addFolder('folder', vfsStream::url('templates/folder')));
        $this->assertEquals($this->engine->getFolders()->get('folder')->getPath(), 'vfs://templates/folder');
    }

    public function testAddFolderWithNamespaceConflict() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template folder "name" is already being used.');
        $this->engine->addFolder('name', vfsStream::url('templates'));
        $this->engine->addFolder('name', vfsStream::url('templates'));
    }

    public function testAddFolderWithInvalidDirectory() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The specified directory path "vfs://does/not/exist" does not exist.');
        $this->engine->addFolder('namespace', vfsStream::url('does/not/exist'));
    }

    public function testRemoveFolder() : void
    {
        vfsStream::create(['folder' => ['template.php' => '']]);
        $this->engine->addFolder('folder', vfsStream::url('templates/folder'));
        $this->assertTrue($this->engine->getFolders()->exists('folder'));
        $this->assertInstanceOf(Engine::class, $this->engine->removeFolder('folder'));
        $this->assertFalse($this->engine->getFolders()->exists('folder'));
    }

    public function testGetFolders() : void
    {
        $this->assertInstanceOf(Folders::class, $this->engine->getFolders());
    }

    public function testAddData() : void
    {
        $this->engine->addData(['name' => 'Jonathan']);
        $data = $this->engine->getData();
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataWithTemplate() : void
    {
        $this->engine->addData(['name' => 'Jonathan'], 'template');
        $data = $this->engine->getData('template');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataWithTemplates() : void
    {
        $this->engine->addData(['name' => 'Jonathan'], ['template1', 'template2']);
        $data = $this->engine->getData('template1');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testRegisterFunction() : void
    {
        vfsStream::create(['template.php' => '<?=$this->uppercase($name)?>']);
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertInstanceOf(Func::class, $this->engine->getFunction('uppercase'));
        $this->assertEquals($this->engine->getFunction('uppercase')->getCallback(), 'strtoupper');
    }

    public function testDropFunction() : void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertTrue($this->engine->doesFunctionExist('uppercase'));
        $this->engine->dropFunction('uppercase');
        $this->assertFalse($this->engine->doesFunctionExist('uppercase'));
    }

    public function testDropInvalidFunction() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function "some_function_that_does_not_exist" was not found.');
        $this->engine->dropFunction('some_function_that_does_not_exist');
    }

    public function testGetFunction() : void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $function = $this->engine->getFunction('uppercase');
        $this->assertInstanceOf(Func::class, $function);
        $this->assertEquals($function->getName(), 'uppercase');
        $this->assertEquals($function->getCallback(), 'strtoupper');
    }

    public function testGetInvalidFunction() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function "some_function_that_does_not_exist" was not found.');
        $this->engine->getFunction('some_function_that_does_not_exist');
    }

    public function testDoesFunctionExist() : void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertTrue($this->engine->doesFunctionExist('uppercase'));
    }

    public function testDoesFunctionNotExist() : void
    {
        $this->assertFalse($this->engine->doesFunctionExist('some_function_that_does_not_exist'));
    }

    public function testLoadExtension()
    {
        $this->assertFalse($this->engine->doesFunctionExist('foo'));
        $this->engine->loadExtension(new DummyExtensionFoo());
        $this->assertTrue($this->engine->doesFunctionExist('foo'));
    }

    public function testLoadExtensions()
    {
        $this->assertFalse($this->engine->doesFunctionExist('foo'));
        $this->assertFalse($this->engine->doesFunctionExist('bar'));
        $this->engine->loadExtensions([
            new DummyExtensionFoo(),
            new DummyExtensionBar(),
        ]);
        $this->assertTrue($this->engine->doesFunctionExist('foo'));
        $this->assertTrue($this->engine->doesFunctionExist('bar'));
    }

    public function testGetTemplatePath() : void
    {
        $this->assertEquals(
            str_replace('\\', '/', $this->engine->path('template')),
            'vfs://templates/template.php'
        );
    }

    public function testTemplateExists() : void
    {
        $this->assertFalse($this->engine->exists('template'));
        vfsStream::create(['template.php' => '']);
        $this->assertTrue($this->engine->exists('template'));
    }

    public function testMakeTemplate() : void
    {
        vfsStream::create(['template.php' => '']);
        $this->assertInstanceOf(Template::class, $this->engine->make('template'));
    }

    public function testRenderTemplate()
    {
        vfsStream::create(['template.php' => 'Hello!']);
        $this->assertEquals($this->engine->render('template'), 'Hello!');
    }
}
