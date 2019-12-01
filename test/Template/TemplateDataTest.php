<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace MobicmsTest;

use Mobicms\Render\Template\TemplateData;
use PHPUnit\Framework\TestCase;

class TemplateDataTest extends TestCase
{
    private $template_data;

    public function setUp(): void
    {
        $this->template_data = new TemplateData();
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(TemplateData::class, $this->template_data);
    }

    public function testAddDataToAllTemplates(): void
    {
        $this->template_data->add(['name' => 'Jonathan']);
        $data = $this->template_data->get();
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataToOneTemplate(): void
    {
        $this->template_data->add(['name' => 'Jonathan'], ['template']);
        $data = $this->template_data->get('template');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataToOneTemplateAgain(): void
    {
        $this->template_data->add(['firstname' => 'Jonathan'], ['template']);
        $this->template_data->add(['lastname' => 'Reinink'], ['template']);
        $data = $this->template_data->get('template');
        $this->assertEquals($data['lastname'], 'Reinink');
    }

    public function testAddDataToSomeTemplates(): void
    {
        $this->template_data->add(['name' => 'Jonathan'], ['template1', 'template2']);
        $data = $this->template_data->get('template1');
        $this->assertEquals($data['name'], 'Jonathan');
    }
}
