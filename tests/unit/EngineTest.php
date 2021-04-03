<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace MobicmsTest\Render;

use Mobicms\Render\Engine;
use Mobicms\Render\Template\TemplateFunction;
use LogicException;
use PHPUnit\Framework\TestCase;
use Throwable;

class EngineTest extends TestCase
{
    private Engine $engine;

    public function setUp(): void
    {
        $this->engine = new Engine();
    }

    public function testGetFileExtension(): void
    {
        $this->assertEquals('phtml', $this->engine->getFileExtension());
    }

    public function testAddFolder(): void
    {
        $this->engine->addFolder('folder', M_PATH_ROOT);
        $this->assertEquals(M_PATH_ROOT, $this->engine->getFolder('folder')[0] . DIRECTORY_SEPARATOR);
    }

    public function testAddFolderWithSearchFolders(): void
    {
        $this->engine->addFolder('folder', M_PATH_ROOT, ['test1', 'test2',]);
        $this->assertEquals(M_PATH_ROOT, $this->engine->getFolder('folder')[0] . DIRECTORY_SEPARATOR);
        $this->assertEquals('test1', $this->engine->getFolder('folder')[1]);
        $this->assertEquals('test2', $this->engine->getFolder('folder')[2]);
    }

    public function testAddFolderWithNamespaceConflict(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template namespace "name" is already being used.');
        $this->engine->addFolder('name', M_PATH_ROOT);
        $this->engine->addFolder('name', M_PATH_ROOT);
    }

    public function testAddFolderWithInvalidDirectory(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The specified directory path "does_not_exist" does not exist.');
        $this->engine->addFolder('namespace', 'does_not_exist');
    }

    public function testGetFolder(): void
    {
        $this->engine->addFolder('name', M_PATH_ROOT);
        $folder = $this->engine->getFolder('name');
        $this->assertEquals(M_PATH_ROOT, $folder[0] . DIRECTORY_SEPARATOR);
    }

    public function testGetNonexistentFolder(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template namespace "name" was not found.');
        $this->engine->getFolder('name');
    }

    public function testAddData(): void
    {
        $this->engine->addData(['name' => 'TestData']);
        $data = $this->engine->getData();
        $this->assertEquals('TestData', $data['name']);
    }

    public function testAddDataWithTemplates(): void
    {
        $this->engine->addData(['name' => 'TestData'], ['template1', 'template2']);
        $data1 = $this->engine->getData('template1');
        $this->assertEquals('TestData', $data1['name']);
    }

    /**
     * @throws Throwable
     */
    public function testRenderTemplate(): void
    {
        $this->engine->addFolder('test', M_PATH_ROOT);
        $this->assertEquals(
            'Hello!',
            $this->engine->render('test::tpl-data', ['var' => 'Hello!'])
        );
    }

    public function testRegisterFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertInstanceOf(TemplateFunction::class, $this->engine->getFunction('uppercase'));
        $this->assertEquals('strtoupper', $this->engine->getFunction('uppercase')->getCallback());

        $this->engine->addFolder('test', M_PATH_ROOT);
        $result = $this->engine->render(
            'test::tpl-func-uppercase',
            ['var' => 'abcdefgh']
        );
        $this->assertEquals('ABCDEFGH', $result);
    }

    public function testRegisterExistFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function name "uppercase" is already registered.');
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->engine->registerFunction('uppercase', 'strtoupper');
    }

    public function testGetFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $function = $this->engine->getFunction('uppercase');
        $this->assertEquals('uppercase', $function->getName());
        $this->assertEquals('strtoupper', $function->getCallback());
    }

    public function testGetInvalidFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template function "some_function_that_does_not_exist" was not found.');
        $this->engine->getFunction('some_function_that_does_not_exist');
    }

    public function testDoesFunctionExist(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertTrue($this->engine->doesFunctionExist('uppercase'));
        $this->assertFalse($this->engine->doesFunctionExist('some_function_that_does_not_exist'));
    }

    public function testLoadExtension(): void
    {
        $this->assertFalse($this->engine->doesFunctionExist('foo'));
        $this->engine->loadExtension(new FakeExtension());
        $this->assertTrue($this->engine->doesFunctionExist('foo'));
    }
}
