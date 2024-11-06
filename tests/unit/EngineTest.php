<?php

declare(strict_types=1);

namespace MobicmsTest\Render;

use InvalidArgumentException;
use Mobicms\Render\Engine;
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
        self::assertEquals('phtml', $this->engine->getFileExtension());
    }

    public function testAddAndGetSeveralFolders(): void
    {
        $this->engine->addPath('folder1', 'ns1');
        $this->engine->addPath('folder2', 'ns1');
        $this->engine->addPath(M_PATH_ROOT);
        self::assertContains('folder1', $this->engine->getPath('ns1'));
        self::assertContains('folder2', $this->engine->getPath('ns1'));
        self::assertContains(rtrim(M_PATH_ROOT, '/\\'), $this->engine->getPath('main'));
    }

    public function testAddFolderWithEmptyNamespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Namespace cannot be empty.');
        $this->engine->addPath('folder', '');
    }

    public function testAddFolderWithEmptyFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must specify folder.');
        $this->engine->addPath('');
    }

    public function testAddFolderWithFoldersConflict(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "test" folder in the "main" namespace already exists.');
        $this->engine->addPath('test');
        $this->engine->addPath('test');
    }

    public function testGetNonexistentFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The template namespace "name" was not found.');
        $this->engine->getPath('name');
    }

    public function testAddData(): void
    {
        $this->engine->addData(['name' => 'TestData']);
        $data = $this->engine->getTemplateData();
        self::assertEquals('TestData', $data['name']);
    }

    public function testAddDataWithTemplates(): void
    {
        $this->engine->addData(['name' => 'TestData'], ['template1', 'template2']);
        $data1 = $this->engine->getTemplateData('template1');
        self::assertEquals('TestData', $data1['name']);
    }

    /**
     * @throws Throwable
     */
    public function testRenderTemplate(): void
    {
        $this->engine->addPath(M_PATH_ROOT);
        self::assertEquals(
            'Hello!',
            $this->engine->render('main::tpl-data', ['var' => 'Hello!'])
        );
    }

    /**
     * @throws Throwable
     */
    public function testRegisterFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->engine->addPath(M_PATH_ROOT);
        $result = $this->engine->render(
            'main::tpl-func-uppercase',
            ['var' => 'abcdefgh']
        );
        self::assertEquals('ABCDEFGH', $result);
    }

    public function testRegisterExistFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The template function name "uppercase" is already registered.');
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->engine->registerFunction('uppercase', 'strtoupper');
    }

    public function testRegisterFunctionWithInvalidName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->engine->registerFunction('invalid-name', 'strtoupper');
    }

    public function testGetFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $function = $this->engine->getFunction('uppercase');
        self::assertEquals('TTT', $function('TTT'));
    }

    public function testGetInvalidFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The template function "some_function_that_does_not_exist" was not found.');
        $this->engine->getFunction('some_function_that_does_not_exist');
    }

    public function testDoesFunctionExist(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        self::assertTrue($this->engine->doesFunctionExist('uppercase'));
        self::assertFalse($this->engine->doesFunctionExist('some_function_that_does_not_exist'));
    }

    public function testLoadExtension(): void
    {
        self::assertFalse($this->engine->doesFunctionExist('foo'));
        $this->engine->loadExtension(new FakeExtension());
        self::assertTrue($this->engine->doesFunctionExist('foo'));
    }
}
