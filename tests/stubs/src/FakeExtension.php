<?php

declare(strict_types=1);

namespace MobicmsTest\Stubs;

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
