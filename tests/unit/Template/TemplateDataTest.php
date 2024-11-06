<?php

declare(strict_types=1);

namespace MobicmsTest\Render\Template;

use Mobicms\Render\Template\TemplateData;
use PHPUnit\Framework\TestCase;

class TemplateDataTest extends TestCase
{
    private TemplateData $templateData;

    public function setUp(): void
    {
        $this->templateData = new TemplateData();
    }

    public function testAddDataToAllTemplates(): void
    {
        $this->templateData->add(['name' => 'TestData']);
        $data = $this->templateData->get();
        self::assertEquals('TestData', $data['name']);
    }

    public function testAddDataToOneTemplate(): void
    {
        $this->templateData->add(['name' => 'TestData'], ['template']);
        $data = $this->templateData->get('template');
        self::assertEquals('TestData', $data['name']);
    }

    public function testAddDataToOneTemplateAgain(): void
    {
        $this->templateData->add(['first' => 'Test'], ['template']);
        $this->templateData->add(['last' => 'Data'], ['template']);
        $data = $this->templateData->get('template');
        self::assertEquals('Data', $data['last']);
    }

    public function testAddDataToSomeTemplates(): void
    {
        $this->templateData->add(['name' => 'TestData'], ['template1', 'template2']);
        $data = $this->templateData->get('template1');
        self::assertEquals('TestData', $data['name']);
    }
}
