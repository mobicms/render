<?php

declare(strict_types=1);

namespace MobicmsTest\Render\Template;

use InvalidArgumentException;
use Mobicms\Render\Engine;
use Mobicms\Render\Template\TemplateName;
use PHPUnit\Framework\TestCase;

class TemplateNameTest extends TestCase
{
    private Engine $engine;

    public function setUp(): void
    {
        $this->engine = new Engine();
        $this->engine->addPath(M_PATH_ROOT);
    }

    public function testGetPath(): void
    {
        $name = new TemplateName($this->engine, 'main::tpl-data');
        $this->assertEquals(
            M_PATH_ROOT . 'tpl-data.phtml',
            $name->resolvePath()
        );
    }

    public function testGetPathWithoutNamespace(): void
    {
        $name = new TemplateName($this->engine, 'tpl-data');
        $this->assertEquals(
            M_PATH_ROOT . 'tpl-data.phtml',
            $name->resolvePath()
        );
    }

    public function testWithInvalidTemplateName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/You must use the folder namespace separator "::" once.$/');
        new TemplateName($this->engine, 'template::tmp::test');
    }

    public function testGetPathWithMultipleFolders(): void
    {
        $engine = new Engine();
        $engine->addPath('somefolder');
        $engine->addPath(M_PATH_ROOT);
        $engine->addPath('anotherfolder');

        $name = new TemplateName($engine, 'tpl-data');
        $this->assertEquals(
            M_PATH_ROOT . 'tpl-data.phtml',
            $name->resolvePath()
        );
    }

    public function testGetPathWithNonexistentTemplate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The template "main::nonexistent" does not exist.');
        $name = new TemplateName($this->engine, 'main::nonexistent');
        $name->resolvePath();
    }
}
