<?php

declare(strict_types=1);

/**
 * This file is part of mobiCMS Content Management System.
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest;

use League\Plates\Template\FileExtension;
use PHPUnit\Framework\TestCase;

class FileExtensionTest extends TestCase
{
    private $fileExtension;

    public function setUp() : void
    {
        $this->fileExtension = new FileExtension();
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(FileExtension::class, $this->fileExtension);
    }

    public function testSetFileExtension()
    {
        $this->assertInstanceOf(FileExtension::class, $this->fileExtension->set('tpl'));
        $this->assertEquals($this->fileExtension->get(), 'tpl');
    }

    public function testSetNullFileExtension()
    {
        $this->assertInstanceOf(FileExtension::class, $this->fileExtension->set(null));
        $this->assertNull($this->fileExtension->get());
    }

    public function testGetFileExtension()
    {
        $this->assertEquals($this->fileExtension->get(), 'php');
    }
}
