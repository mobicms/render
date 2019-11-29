<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace MobicmsTest\Extension;

use Mobicms\Render\ExtensionInterface;

interface DummyExtensionInterface extends ExtensionInterface
{
    public function foo();
}
