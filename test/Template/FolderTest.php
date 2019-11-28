<?php

declare(strict_types=1);

/**
 * This file is part of mobiCMS Content Management System.
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest;

use League\Plates\Template\Folder;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FolderTest extends TestCase
{
    private $folder;

    public function setUp() : void
    {
        vfsStream::setup('templates');
        $this->folder = new Folder('folder', vfsStream::url('templates'));
    }

    public function testCanCreateInstance() : void
    {
        $this->assertInstanceOf(Folder::class, $this->folder);
    }

    public function testSetAndGetName() : void
    {
        $this->folder->setName('name');
        $this->assertEquals($this->folder->getName(), 'name');
    }

    public function testSetAndGetPath() : void
    {
        vfsStream::create(['folder' => []]);
        $this->folder->setPath(vfsStream::url('templates/folder'));
        $this->assertEquals($this->folder->getPath(), vfsStream::url('templates/folder'));
    }

    public function testSetInvalidPath()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The specified directory path "vfs://does/not/exist" does not exist.');
        $this->folder->setPath(vfsStream::url('does/not/exist'));
    }

    public function testSetAndGetFallback()
    {
        $this->assertFalse($this->folder->getFallback());
        $this->folder->setFallback(true);
        $this->assertTrue($this->folder->getFallback());
    }
}
