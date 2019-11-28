<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest;

use Mobicms\Render\Template\Folder;
use Mobicms\Render\Template\Folders;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FoldersTest extends TestCase
{
    private $folders;

    public function setUp() : void
    {
        vfsStream::setup('templates');
        $this->folders = new Folders();
    }

    public function testCanCreateInstance() : void
    {
        $this->assertInstanceOf(Folders::class, $this->folders);
    }

    public function testAddFolder() : void
    {
        $this->assertInstanceOf(Folders::class, $this->folders->add('name', vfsStream::url('templates')));
        $this->assertEquals($this->folders->get('name')->getPath(), 'vfs://templates');
    }

    public function testAddFolderWithNamespaceConflict() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template folder "name" is already being used.');
        $this->folders->add('name', vfsStream::url('templates'));
        $this->folders->add('name', vfsStream::url('templates'));
    }

    public function testAddFolderWithInvalidDirectory() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The specified directory path "vfs://does/not/exist" does not exist.');
        $this->folders->add('name', vfsStream::url('does/not/exist'));
    }

    public function testRemoveFolder() : void
    {
        $this->folders->add('folder', vfsStream::url('templates'));
        $this->assertTrue($this->folders->exists('folder'));
        $this->assertInstanceOf(Folders::class, $this->folders->remove('folder'));
        $this->assertFalse($this->folders->exists('folder'));
    }

    public function testRemoveFolderWithInvalidDirectory() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template folder "name" was not found.');
        $this->folders->remove('name');
    }

    public function testGetFolder() : void
    {
        $this->folders->add('name', vfsStream::url('templates'));
        $this->assertInstanceOf(Folder::class, $this->folders->get('name'));
        $this->assertEquals($this->folders->get('name')->getPath(), vfsStream::url('templates'));
    }

    public function testGetNonExistentFolder() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template folder "name" was not found.');
        $this->assertInstanceOf(Folder::class, $this->folders->get('name'));
    }

    public function testFolderExists() : void
    {
        $this->assertFalse($this->folders->exists('name'));
        $this->folders->add('name', vfsStream::url('templates'));
        $this->assertTrue($this->folders->exists('name'));
    }
}
