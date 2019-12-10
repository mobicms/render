<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace MobicmsTest;

use Mobicms\Render\Engine;
use Mobicms\Render\Template\Template;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    private $template;

    public function setUp(): void
    {
        vfsStream::setup('templates');
        $engine = new Engine();
        $engine->addFolder('folder', vfsStream::url('templates'));
        $engine->registerFunction('uppercase', 'strtoupper');
        $this->template = new Template($engine, 'folder::template');
    }

    public function testCanCreateInstance(): void
    {
        $this->assertInstanceOf(Template::class, $this->template);
    }

    /**
     * @throws \Throwable
     */
    public function testCanCallFunction(): void
    {
        vfsStream::create(['template.phtml' => '<?php echo $this->uppercase("jonathan") ?>']);
        $this->assertEquals($this->template->render(), 'JONATHAN');
    }

    /**
     * @throws \Throwable
     */
    public function testAssignData(): void
    {
        vfsStream::create(['template.phtml' => '<?php echo $name ?>']);
        $this->template->data(['name' => 'Jonathan']);
        $this->assertEquals($this->template->render(), 'Jonathan');
    }

    public function testGetData(): void
    {
        $data = ['name' => 'Jonathan'];
        $this->template->data($data);
        $this->assertEquals($this->template->data(), $data);
    }

    /**
     * @throws \Throwable
     */
    public function testRender(): void
    {
        vfsStream::create(['template.phtml' => 'Hello World']);
        $this->assertEquals($this->template->render(), 'Hello World');
    }

    public function testRenderViaToStringMagicMethod(): void
    {
        vfsStream::create(['template.phtml' => 'Hello World']);
        $actual = (string) $this->template;
        $this->assertEquals($actual, 'Hello World');
    }

    /**
     * @throws \Throwable
     */
    public function testRenderWithData(): void
    {
        vfsStream::create(['template.phtml' => '<?php echo $name ?>']);
        $this->assertEquals($this->template->render(['name' => 'Jonathan']), 'Jonathan');
    }

    /**
     * @throws \Throwable
     */
    public function testRenderDoesNotExist(): void
    {
        $this->expectException(LogicException::class);
        $this->template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testRenderException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('error');
        vfsStream::create(['template.phtml' => '<?php throw new Exception("error"); ?>']);
        $this->template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testLayout(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->layout("folder::layout") ?>',
                'layout.phtml'   => 'Hello World',
            ]
        );

        $this->assertEquals($this->template->render(), 'Hello World');
    }

    /**
     * @throws \Throwable
     */
    public function testSectionReplace(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->layout("folder::layout")?><?php $this->sectionReplace("test", "Hello World") ?>',
                'layout.phtml'   => '<?php echo $this->section("test") ?>',
            ]
        );
        $this->assertEquals($this->template->render(), 'Hello World');
    }

    public function testSectionAppend(): void
    {
        vfsStream::create(
            [
                'template.phtml' => implode(
                    '\n',
                    [
                        '<?php $this->layout("folder::layout")?><?php $this->sectionAppend("test", "Hello World") ?>',
                        '<?php $this->layout("folder::layout")?><?php $this->sectionAppend("test", "!!!") ?>',
                    ]
                ),
                'layout.phtml'   => '<?php echo $this->section("test") ?>',
            ]
        );
        $this->assertEquals($this->template->render(), 'Hello World!!!');
    }

    /**
     * @throws \Throwable
     */
    public function testSection(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->layout("folder::layout")?><?php $this->start("test") ?>Hello World<?php $this->stop() ?>',
                'layout.phtml'   => '<?php echo $this->section("test") ?>',
            ]
        );
        $this->assertEquals($this->template->render(), 'Hello World');
    }

    /**
     * @throws \Throwable
     */
    public function testReplaceSection(): void
    {
        vfsStream::create(
            [
                'template.phtml' => implode(
                    '\n',
                    [
                        '<?php $this->layout("folder::layout")?><?php $this->start("test") ?>Hello World<?php $this->stop() ?>',
                        '<?php $this->layout("folder::layout")?><?php $this->start("test") ?>See this instead!<?php $this->stop() ?>',
                    ]
                ),
                'layout.phtml'   => '<?php echo $this->section("test") ?>',
            ]
        );
        $this->assertEquals($this->template->render(), 'See this instead!');
    }

    /**
     * @throws \Throwable
     */
    public function testStartSectionWithInvalidName(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The section name "content" is reserved.');
        vfsStream::create(['template.phtml' => '<?php $this->start("content") ?>']);
        $this->template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testNestSectionWithinAnotherSection(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You cannot nest sections within other sections.');
        vfsStream::create(['template.phtml' => '<?php $this->start("section1") ?><?php $this->start("section2") ?>']);
        $this->template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testStopSectionBeforeStarting(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You must start a section before you can stop it.');
        vfsStream::create(['template.phtml' => '<?php $this->stop() ?>']);
        $this->template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testSectionDefaultValue(): void
    {
        vfsStream::create(['template.phtml' => '<?php echo $this->section("test", "Default value") ?>']);
        $this->assertEquals($this->template->render(), 'Default value');
    }

    /**
     * @throws \Throwable
     */
    public function testNullSection(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->layout("folder::layout") ?>',
                'layout.phtml'   => '<?php if (is_null($this->section("test"))) echo "NULL" ?>',
            ]
        );
        $this->assertEquals($this->template->render(), 'NULL');
    }

    /**
     * @throws \Throwable
     */
    public function testPushSection(): void
    {
        vfsStream::create(
            [
                'template.phtml' => implode(
                    '\n',
                    [
                        '<?php $this->layout("folder::layout")?>',
                        '<?php $this->push("scripts") ?><script src="example1.js"></script><?php $this->end() ?>',
                        '<?php $this->push("scripts") ?><script src="example2.js"></script><?php $this->end() ?>',
                    ]
                ),
                'layout.phtml'   => '<?php echo $this->section("scripts") ?>',
            ]
        );
        $this->assertEquals(
            $this->template->render(),
            '<script src="example1.js"></script><script src="example2.js"></script>'
        );
    }

    /**
     * @throws \Throwable
     */
    public function testPushWithMultipleSections(): void
    {
        vfsStream::create(
            [
                'template.phtml' => implode(
                    '\n',
                    [
                        '<?php $this->layout("folder::layout")?>',
                        '<?php $this->push("scripts") ?><script src="example1.js"></script><?php $this->end() ?>',
                        '<?php $this->start("test") ?>test<?php $this->stop() ?>',
                        '<?php $this->push("scripts") ?><script src="example2.js"></script><?php $this->end() ?>',
                    ]
                ),
                'layout.phtml'   => implode(
                    '\n',
                    [
                        '<?php echo $this->section("test") ?>',
                        '<?php echo $this->section("scripts") ?>',
                    ]
                ),
            ]
        );
        $this->assertEquals(
            $this->template->render(),
            'test\n<script src="example1.js"></script><script src="example2.js"></script>'
        );
    }

    /**
     * @throws \Throwable
     */
    public function testFetchFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->fetch("folder::fetched") ?>',
                'fetched.phtml'  => 'Hello World',
            ]
        );
        $this->assertEquals($this->template->render(), 'Hello World');
    }

    /**
     * @throws \Throwable
     */
    public function testInsertFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php $this->insert("folder::inserted") ?>',
                'inserted.phtml' => 'Hello World',
            ]
        );
        $this->assertEquals($this->template->render(), 'Hello World');
    }

    /**
     * @throws \Throwable
     */
    public function testBatchFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->batch("Jonathan", "uppercase|strtolower") ?>',
            ]
        );
        $this->assertEquals($this->template->render(), 'jonathan');
    }

    /**
     * @throws \Throwable
     */
    public function testBatchFunctionWithInvalidFunction(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The batch function could not find the "function_that_does_not_exist" function.');
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->batch("Jonathan", "function_that_does_not_exist") ?>',
            ]
        );
        $this->template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testEscapeFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->e("<strong>Jonathan</strong>") ?>',
            ]
        );
        $this->assertEquals($this->template->render(), '&lt;strong&gt;Jonathan&lt;/strong&gt;');
    }

    /**
     * @throws \Throwable
     */
    public function testEscapeFunctionBatch(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->e("<strong>Jonathan</strong>", "strtoupper|strrev") ?>',
            ]
        );
        $this->assertEquals($this->template->render(), '&gt;GNORTS/&lt;NAHTANOJ&gt;GNORTS&lt;');
    }

    /**
     * @throws \Throwable
     */
    public function testEscapeShortcutFunction(): void
    {
        vfsStream::create(
            [
                'template.phtml' => '<?php echo $this->e("<strong>Jonathan</strong>") ?>',
            ]
        );
        $this->assertEquals($this->template->render(), '&lt;strong&gt;Jonathan&lt;/strong&gt;');
    }
}
