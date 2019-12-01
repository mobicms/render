<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest;

use Mobicms\Render\Engine;
use Mobicms\Render\Template\TemplateName;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class TemplateNameTest extends TestCase
{
    private $engine;

    public function setUp(): void
    {
        vfsStream::setup('templates');
        vfsStream::create(
            [
                'folder' => ['template.phtml' => ''],
                'theme'  => ['index.phtml' => ''],
            ]
        );

        $this->engine = new Engine();
        $this->engine->addFolder('folder', vfsStream::url('templates/folder'));
    }

    public function testCanCreateInstance(): void
    {
        $name = new TemplateName($this->engine, 'folder::template');
        $this->assertInstanceOf(TemplateName::class, $name);
    }

    public function testCanCreateInstanceWithInvalidTemplateName(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/You must use the folder namespace separator "::" once.$/');
        new TemplateName($this->engine, 'template');
    }

    public function testGetPath(): void
    {
        $name = new TemplateName($this->engine, 'folder::template');
        $this->assertEquals(
            str_replace('\\', '/', $name->getPath()),
            vfsStream::url('templates/folder/template.phtml')
        );
    }

    public function testGetPathWithMultipleFolders(): void
    {
        $engine = new Engine();
        $engine->addFolder(
            'folder',
            vfsStream::url('templates/folder'),
            [vfsStream::url('templates/theme')]
        );

        $name = new TemplateName($engine, 'folder::index');
        $this->assertEquals(
            str_replace('\\', '/', $name->getPath()),
            vfsStream::url('templates/theme/index.phtml')
        );
    }

    public function testGetPathWithNonexistentTemplate(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template "folder::nonexistent" does not exist.');
        $name = new TemplateName($this->engine, 'folder::nonexistent');
        $name->getPath();
    }
}
