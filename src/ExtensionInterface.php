<?php

declare(strict_types=1);

namespace Mobicms\Render;

/**
 * A common interface for extensions.
 */
interface ExtensionInterface
{
    public function register(Engine $engine): void;
}
