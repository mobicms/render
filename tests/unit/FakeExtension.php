<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace MobicmsTest\Render;

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
