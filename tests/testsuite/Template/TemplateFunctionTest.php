<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace Test\Suite\Template;

use Mobicms\Render\Template\TemplateFunction;
use LogicException;
use Test\Support\FakeExtension;
use PHPUnit\Framework\TestCase;

class TemplateFunctionTest extends TestCase
{
    private $function;

    public function setUp(): void
    {
        $this->function = new TemplateFunction(
            'uppercase',
            function ($string) {
                return strtoupper($string);
            }
        );
    }

    public function testCanCreateInstance(): void
    {
        $this->assertInstanceOf(TemplateFunction::class, $this->function);
    }

    public function testSetAndGetName(): void
    {
        $this->assertInstanceOf(TemplateFunction::class, $this->function->setName('test'));
        $this->assertEquals($this->function->getName(), 'test');
    }

    public function testSetInvalidName(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Not a valid function name.');
        $this->function->setName('invalid-function-name');
    }

    public function testSetAndGetCallback(): void
    {
        $this->assertInstanceOf(TemplateFunction::class, $this->function->setCallback('strtolower'));
        $this->assertEquals($this->function->getCallback(), 'strtolower');
    }

    public function testSetInvalidCallback(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Not a valid function callback.');
        $this->function->setCallback(null);
    }

    public function testFunctionCall(): void
    {
        $this->assertEquals($this->function->call(['Jonathan']), 'JONATHAN');
    }

    public function testExtensionFunctionCall(): void
    {
        $extension = $this->createPartialMock(FakeExtension::class, ['register', 'foo']);
        $extension->method('foo')->willReturn('bar');
        $this->function->setCallback([$extension, 'foo']);
        $this->assertEquals($this->function->call(), 'bar');
    }
}
