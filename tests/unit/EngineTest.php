<?php

declare(strict_types=1);

namespace MobicmsTest\Render;

use InvalidArgumentException;
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

    public function testAddAndGetSeveralFolders(): void
    {
        $this->engine->addFolder('ns1', 'folder1');
        $this->engine->addFolder('ns1', 'folder2');
        $this->engine->addFolder('ns2', 'folder3');
        $this->assertContains('folder1', $this->engine->getFolder('ns1'));
        $this->assertContains('folder2', $this->engine->getFolder('ns1'));
        $this->assertContains('folder3', $this->engine->getFolder('ns2'));
    }

    public function testAddFolderWithEmptyNamespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must specify namespace.');
        $this->engine->addFolder('', 'folder');
    }

    public function testAddFolderWithEmptyFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must specify folder.');
        $this->engine->addFolder('ns', '');
    }

    public function testAddFolderWithFoldersConflict(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "test" folder in the "ns" namespace already exists.');
        $this->engine->addFolder('ns', 'test');
        $this->engine->addFolder('ns', 'test');
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

    /**
     * @throws Throwable
     */
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
