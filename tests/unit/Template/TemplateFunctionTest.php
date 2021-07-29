<?php

declare(strict_types=1);

namespace MobicmsTest\Render\Template;

use Mobicms\Render\Template\TemplateFunction;
use LogicException;
use PHPUnit\Framework\TestCase;

class TemplateFunctionTest extends TestCase
{
    public function testCanCreateInstance(): TemplateFunction
    {
        $instance = new TemplateFunction(
            function (string $string) {
                return strtoupper($string);
            }
        );
        $this->assertInstanceOf(TemplateFunction::class, $instance);
        return $instance;
    }

    /**
     * @depends testCanCreateInstance
     */
    public function testFunctionCall(TemplateFunction $instance): void
    {
        $this->assertEquals('TESTDATA', $instance->call(['TestData']));
    }

//    public function testSetInvalidName(): void
//    {
//        $this->expectException(LogicException::class);
//        $this->expectExceptionMessage('Not a valid function name.');
//        $this->function->checkName('invalid-function-name');
//    }

//    public function testSetAndGetCallback(): void
//    {
//        $this->assertInstanceOf(TemplateFunction::class, $this->function->checkCallback('strtolower'));
//        $this->assertEquals('strtolower', $this->function->getCallback());
//    }

//    public function testSetInvalidCallback(): void
//    {
//        $this->expectException(LogicException::class);
//        $this->expectExceptionMessage('Not a valid function callback.');
//        $this->function->checkCallback(null);
//    }

//    public function testExtensionFunctionCall(): void
//    {
//        $this->function->checkCallback(
//            function () {
//                return 'bar';
//            }
//        );
//        $this->assertEquals('bar', $this->function->call());
//    }
}
