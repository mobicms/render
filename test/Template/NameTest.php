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
use League\Plates\Template\Folder;
use League\Plates\Template\Name;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    private $engine;

    public function setUp() : void
    {
        vfsStream::setup('templates');
        vfsStream::create(
            [
                'template.php' => '',
                'fallback.php' => '',
                'folder'       => ['template.php' => ''],
            ]
        );

        $this->engine = new Engine(vfsStream::url('templates'));
        $this->engine->addFolder('folder', vfsStream::url('templates/folder'), true);
    }

    public function testCanCreateInstance() : void
    {
        $this->assertInstanceOf(Name::class, new Name($this->engine, 'template'));
    }

    public function testGetEngine() : void
    {
        $name = new Name($this->engine, 'template');
        $this->assertInstanceOf(Engine::class, $name->getEngine());
    }

    public function testGetName() : void
    {
        $name = new Name($this->engine, 'template');
        $this->assertEquals($name->getName(), 'template');
    }

    public function testGetFolder() : void
    {
        $name = new Name($this->engine, 'folder::template');
        $folder = $name->getFolder();
        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals($name->getFolder()->getName(), 'folder');
    }

    public function testGetFile() : void
    {
        $name = new Name($this->engine, 'template');
        $this->assertEquals($name->getFile(), 'template.php');
    }

    public function testGetPath() : void
    {
        $name = new Name($this->engine, 'template');
        $this->assertEquals(str_replace('\\', '/', $name->getPath()), vfsStream::url('templates/template.php'));
    }

    public function testGetPathWithFolder() : void
    {
        $name = new Name($this->engine, 'folder::template');
        $this->assertEquals(str_replace('\\', '/', $name->getPath()), vfsStream::url('templates/folder/template.php'));
    }

    public function testGetPathWithFolderFallback() : void
    {
        $name = new Name($this->engine, 'folder::fallback');
        $this->assertEquals(str_replace('\\', '/', $name->getPath()), vfsStream::url('templates/fallback.php'));
    }

    public function testTemplateExists() : void
    {
        $name = new Name($this->engine, 'template');
        $this->assertEquals($name->doesPathExist(), true);
    }

    public function testTemplateDoesNotExist() : void
    {
        $name = new Name($this->engine, 'missing');
        $this->assertEquals($name->doesPathExist(), false);
    }

    public function testParse() : void
    {
        $name = new Name($this->engine, 'template');
        $this->assertEquals($name->getName(), 'template');
        $this->assertNull($name->getFolder());
        $this->assertEquals($name->getFile(), 'template.php');
    }

    public function testParseWithNoDefaultDirectory() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The default directory has not been defined.');
        $this->engine->setDirectory(null);
        $name = new Name($this->engine, 'template');
        $name->getPath();
    }

    public function testParseWithEmptyTemplateName() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template name cannot be empty.');
        new Name($this->engine, '');
    }

    public function testParseWithFolder() : void
    {
        $name = new Name($this->engine, 'folder::template');
        $this->assertEquals($name->getName(), 'folder::template');
        $this->assertEquals($name->getFolder()->getName(), 'folder');
        $this->assertEquals($name->getFile(), 'template.php');
    }

    public function testParseWithFolderAndEmptyTemplateName() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template name cannot be empty.');
        new Name($this->engine, 'folder::');
    }

    public function testParseWithInvalidName() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Do not use the folder namespace separator "::" more than once.');
        new Name($this->engine, 'folder::template::wrong');
    }

    public function testParseWithNoFileExtension() : void
    {
        $this->engine->setFileExtension(null);
        $name = new Name($this->engine, 'template.php');
        $this->assertEquals($name->getName(), 'template.php');
        $this->assertEquals($name->getFolder(), null);
        $this->assertEquals($name->getFile(), 'template.php');
    }
}
