<?php

declare(strict_types=1);

namespace MobicmsTest\Render\Template;

use Mobicms\Render\Engine;
use Mobicms\Render\Template\Template;
use LogicException;
use PHPUnit\Framework\TestCase;
use Throwable;

class TemplateTest extends TestCase
{
    private Engine $engine;

    public function setUp(): void
    {
        $this->engine = new Engine();
        $this->engine->addPath(M_PATH_ROOT);
    }

    public function testRender(): void
    {
        $template = new Template($this->engine, 'main::tpl-empty');
        self::assertEquals('Empty', $template->render());
    }

    public function testRenderViaToStringMagicMethod(): void
    {
        $template = new Template($this->engine, 'main::tpl-empty');
        self::assertEquals('Empty', (string) $template);
    }

    /**
     * @throws Throwable
     */
    public function testAssignData(): void
    {
        $data = ['var' => 'TestData'];
        $template = new Template($this->engine, 'main::tpl-data');
        $template->data($data);
        self::assertEquals($template->data(), $data);
        self::assertEquals('TestData', $template->render());
        self::assertEquals('Test', $template->render(['var' => 'Test']));
    }

    /**
     * @throws Throwable
     */
    public function testCanCallFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $template = new Template($this->engine, 'main::tpl-func-uppercase');
        self::assertEquals('TESTDATA', $template->render(['var' => 'TestData']));
    }


    /**
     * @throws Throwable
     */
    public function testRenderDoesNotExist(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The template "main::missing" does not exist.');
        $template = new Template($this->engine, 'main::missing');
        $template->render();
    }

    /**
     * @throws Throwable
     */
    public function testRenderException(): void
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('error');
        $template = new Template($this->engine, 'main::tpl-exception');
        $template->render();
    }

    /**
     * @throws Throwable
     */
    public function testLayout(): void
    {
        $template = new Template($this->engine, 'main::tpl-layout');
        self::assertEquals('Hello User!', $template->render());
    }

    /**
     * @throws Throwable
     */
    public function testSectionReplace(): void
    {
        $template = new Template($this->engine, 'main::tpl-section-replace');
        self::assertEquals('Hello World!', $template->render());
    }

    /**
     * @throws Throwable
     */
    public function testSectionAppend(): void
    {
        $template = new Template($this->engine, 'main::tpl-section-append');
        self::assertEquals('Hello Beautiful World!', $template->render());
    }

    /**
     * @throws Throwable
     */
    public function testSection(): void
    {
        $template = new Template($this->engine, 'main::tpl-section');
        self::assertEquals('Hello All!' . "\n", $template->render());
    }

    /**
     * @throws Throwable
     */
    public function testStartSectionWithReservedName(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The section name "content" is reserved.');
        $template = new Template($this->engine, 'main::tpl-section-reserved');
        $template->render();
    }

    /**
     * @throws Throwable
     */
    public function testNestSectionWithinAnotherSection(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You cannot nest sections within other sections.');
        $template = new Template($this->engine, 'main::tpl-section-nested');
        $template->render();
    }

    /**
     * @throws Throwable
     */
    public function testStopSectionBeforeStarting(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You must start a section before you can stop it.');
        $template = new Template($this->engine, 'main::tpl-section-stop');
        $template->render();
    }

    /**
     * @throws Throwable
     */
    public function testPushSection(): void
    {
        $template = new Template($this->engine, 'main::tpl-section-push');
        self::assertEquals('Hello Beautiful World!', $template->render());
    }

    /**
     * @throws Throwable
     */
    public function testFetchFunction(): void
    {
        $template = new Template($this->engine, 'main::tpl-fetch');
        self::assertEquals('Empty', $template->render());
    }

    /**
     * @throws Throwable
     */
    public function testBatchFunction(): void
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $template = new Template($this->engine, 'main::tpl-batch');
        self::assertEquals('testdata', $template->render());
    }

    /**
     * @throws Throwable
     */
    public function testBatchFunctionWithInvalidFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The batch function could not find the "uppercase" function.');
        $template = new Template($this->engine, 'main::tpl-batch');
        self::assertEquals('testdata', $template->render());
    }

    /**
     * @throws Throwable
     */
    public function testEscapeFunction(): void
    {
        $data = ['var' => '&"\'<>'];
        $template = new Template($this->engine, 'main::tpl-data');
        self::assertEquals('&amp;&quot;&#039;&lt;&gt;', $template->render($data));
    }

    /**
     * @throws Throwable
     */
    public function testEscapeFunctionBatch(): void
    {
        $template = new Template($this->engine, 'main::tpl-escape-batch');
        self::assertEquals('&gt;GNORTS/&lt;ATADTSET&gt;GNORTS&lt;', $template->render());
    }
}
