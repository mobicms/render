<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest;

use League\Plates\Template\Data;
use LogicException;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private $template_data;

    public function setUp() : void
    {
        $this->template_data = new Data();
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(Data::class, $this->template_data);
    }

    public function testAddDataToAllTemplates() : void
    {
        $this->template_data->add(['name' => 'Jonathan']);
        $data = $this->template_data->get();
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataToOneTemplate() : void
    {
        $this->template_data->add(['name' => 'Jonathan'], 'template');
        $data = $this->template_data->get('template');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataToOneTemplateAgain() : void
    {
        $this->template_data->add(['firstname' => 'Jonathan'], 'template');
        $this->template_data->add(['lastname' => 'Reinink'], 'template');
        $data = $this->template_data->get('template');
        $this->assertEquals($data['lastname'], 'Reinink');
    }

    public function testAddDataToSomeTemplates() : void
    {
        $this->template_data->add(['name' => 'Jonathan'], ['template1', 'template2']);
        $data = $this->template_data->get('template1');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataWithInvalidTemplateFileType() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The templates variable must be null, an array or a string, integer given.');
        $this->template_data->add(['name' => 'Jonathan'], 123);
    }
}
