<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest;

use Mobicms\Render\Template\Functions;
use LogicException;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    private $functions;

    public function setUp() : void
    {
        $this->functions = new Functions();
    }

    public function testCanCreateInstance() : void
    {
        $this->assertInstanceOf(Functions::class, $this->functions);
    }

    public function testAddAndGetFunction() : void
    {
        $this->assertInstanceOf(Functions::class, $this->functions->add('upper', 'strtoupper'));
        $this->assertEquals($this->functions->get('upper')->getCallback(), 'strtoupper');
    }

    public function testAddFunctionConflict() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function name "upper" is already registered.');
        $this->functions->add('upper', 'strtoupper');
        $this->functions->add('upper', 'strtoupper');
    }

    public function testGetNonExistentFunction() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function "foo" was not found.');
        $this->functions->get('foo');
    }

    public function testRemoveFunction() : void
    {
        $this->functions->add('upper', 'strtoupper');
        $this->assertTrue($this->functions->exists('upper'));
        $this->functions->remove('upper');
        $this->assertFalse($this->functions->exists('upper'));
    }

    public function testRemoveNonExistentFunction()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function "foo" was not found.');
        $this->functions->remove('foo');
    }

    public function testFunctionExists()
    {
        $this->assertFalse($this->functions->exists('upper'));
        $this->functions->add('upper', 'strtoupper');
        $this->assertTrue($this->functions->exists('upper'));
    }
}
