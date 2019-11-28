<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace MobicmsTest\Extension;

use Mobicms\Render\ExtensionInterface;

interface DummyExtensionInterface extends ExtensionInterface
{
    public function foo();
}
