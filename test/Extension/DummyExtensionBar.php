<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace MobicmsTest\Extension;

use Mobicms\Render\Engine;
use Mobicms\Render\ExtensionInterface;

class DummyExtensionBar implements ExtensionInterface
{
    public function register(Engine $engine): void
    {
        $engine->registerFunction('bar', [$this, 'bar']);
    }

    public function bar(): string
    {
        return 'DummyExtensionBar';
    }
}
