<?php

declare(strict_types=1);

namespace MobicmsTest\Render\Template;

use Mobicms\Render\Engine;
use Mobicms\Render\Template\TemplateName;
use LogicException;
use PHPUnit\Framework\TestCase;

class TemplateNameTest extends TestCase
{
    private Engine $engine;

    public function setUp(): void
    {
        $this->engine = new Engine();
        $this->engine->addPath(M_PATH_ROOT);
    }

    public function testCanCreateInstanceWithInvalidTemplateName(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/You must use the folder namespace separator "::" once.$/');
        new TemplateName($this->engine, 'template');
    }

    public function testGetPath(): void
    {
        $name = new TemplateName($this->engine, 'main::tpl-data');
        $this->assertEquals(
            M_PATH_ROOT . 'tpl-data.phtml',
            $name->getPath()
        );
    }

    public function testGetPathWithMultipleFolders(): void
    {
        $engine = new Engine();
        $engine->addPath('somefolder');
        $engine->addPath(M_PATH_ROOT);
        $engine->addPath('anotherfolder');

        $name = new TemplateName($engine, 'main::tpl-data');
        $this->assertEquals(
            M_PATH_ROOT . 'tpl-data.phtml',
            $name->getPath()
        );
    }

    public function testGetPathWithNonexistentTemplate(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template "main::nonexistent" does not exist.');
        $name = new TemplateName($this->engine, 'main::nonexistent');
        $name->getPath();
    }
}
