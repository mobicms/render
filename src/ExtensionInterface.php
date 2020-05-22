<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mobicms\Render;

/**
 * A common interface for extensions.
 */
interface ExtensionInterface
{
    public function register(Engine $engine): void;
}
