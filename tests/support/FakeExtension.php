<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace Test\Support;

use Mobicms\Render\Engine;
use Mobicms\Render\ExtensionInterface;

class FakeExtension implements ExtensionInterface
{
    public function register(Engine $engine): void
    {
        $engine->registerFunction('foo', [$this, 'foo']);
    }

    public function foo(): string
    {
        return 'FakeExtension';
    }
}
