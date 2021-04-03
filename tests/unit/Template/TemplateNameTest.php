<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
 */

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
        $this->engine->addFolder('test', M_PATH_ROOT);
    }

    public function testCanCreateInstanceWithInvalidTemplateName(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/You must use the folder namespace separator "::" once.$/');
        new TemplateName($this->engine, 'template');
    }

    public function testGetPath(): void
    {
        $name = new TemplateName($this->engine, 'test::tpl-data');
        $this->assertEquals(
            M_PATH_ROOT . 'tpl-data.phtml',
            $name->getPath()
        );
    }

    public function testGetPathWithMultipleFolders(): void
    {
        $engine = new Engine();
        $engine->addFolder(
            'test',
            M_PATH_ROOT,
            ['somefolder', 'anotherfolder']
        );

        $name = new TemplateName($engine, 'test::tpl-data');
        $this->assertEquals(
            M_PATH_ROOT . 'tpl-data.phtml',
            $name->getPath()
        );
    }

    public function testGetPathWithNonexistentTemplate(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template "test::nonexistent" does not exist.');
        $name = new TemplateName($this->engine, 'test::nonexistent');
        $name->getPath();
    }
}
