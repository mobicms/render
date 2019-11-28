<?php

declare(strict_types=1);

/**
 * This file is part of mobiCMS Content Management System.
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest\Extension;

use League\Plates\Engine;
use League\Plates\ExtensionInterface;

class DummyExtensionBar implements ExtensionInterface
{
    public function register(Engine $engine) : void
    {
        $engine->registerFunction('bar', [$this, 'bar']);
    }

    public function bar() : string
    {
        return 'DummyExtensionBar';
    }
}
