<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
 */

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
        $this->templateData->add(['name' => 'Jonathan']);
        $data = $this->templateData->get();
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataToOneTemplate(): void
    {
        $this->templateData->add(['name' => 'Jonathan'], ['template']);
        $data = $this->templateData->get('template');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataToOneTemplateAgain(): void
    {
        $this->templateData->add(['firstname' => 'Jonathan'], ['template']);
        $this->templateData->add(['lastname' => 'Reinink'], ['template']);
        $data = $this->templateData->get('template');
        $this->assertEquals($data['lastname'], 'Reinink');
    }

    public function testAddDataToSomeTemplates(): void
    {
        $this->templateData->add(['name' => 'Jonathan'], ['template1', 'template2']);
        $data = $this->templateData->get('template1');
        $this->assertEquals($data['name'], 'Jonathan');
    }
}
