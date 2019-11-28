<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest;

use League\Plates\Template\Directory;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    private $directory;

    public function setUp() : void
    {
        vfsStream::setup('templates');
        $this->directory = new Directory();
    }

    public function testCanCreateInstance() : void
    {
        $this->assertInstanceOf(Directory::class, $this->directory);
    }

    public function testSetDirectory() : void
    {
        $this->assertInstanceOf(Directory::class, $this->directory->set(vfsStream::url('templates')));
        $this->assertEquals($this->directory->get(), vfsStream::url('templates'));
    }

    public function testSetNullDirectory() : void
    {
        $this->assertInstanceOf(Directory::class, $this->directory->set(null));
        $this->assertNull($this->directory->get());
    }

    public function testSetInvalidDirectory() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The specified path "vfs://does/not/exist" does not exist.');
        $this->directory->set(vfsStream::url('does/not/exist'));
    }

    public function testGetDirectory() : void
    {
        $this->assertNull($this->directory->get());
    }
}
