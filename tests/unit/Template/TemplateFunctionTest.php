<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace MobicmsTest\Render\Template;

use Mobicms\Render\Template\TemplateFunction;
use LogicException;
use PHPUnit\Framework\TestCase;

class TemplateFunctionTest extends TestCase
{
    private TemplateFunction $function;

    public function setUp(): void
    {
        $this->function = new TemplateFunction(
            'uppercase',
            function (string $string) {
                return strtoupper($string);
            }
        );
    }

    public function testSetAndGetName(): void
    {
        $this->assertInstanceOf(TemplateFunction::class, $this->function->setName('test'));
        $this->assertEquals('test', $this->function->getName());
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
        $this->assertEquals('strtolower', $this->function->getCallback());
    }

    public function testSetInvalidCallback(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Not a valid function callback.');
        $this->function->setCallback(null);
    }

    public function testFunctionCall(): void
    {
        $this->assertEquals('TESTDATA', $this->function->call(['TestData']));
    }

    public function testExtensionFunctionCall(): void
    {
        $this->function->setCallback(
            function () {
                return 'bar';
            }
        );
        $this->assertEquals('bar', $this->function->call());
    }
}
