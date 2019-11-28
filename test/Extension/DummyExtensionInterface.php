<?php

declare(strict_types=1);

namespace MobicmsTest\Extension;

use League\Plates\ExtensionInterface;

interface DummyExtensionInterface extends ExtensionInterface
{
    public function foo();
}
