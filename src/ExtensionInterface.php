<?php

namespace League\Plates;

/**
 * A common interface for extensions.
 */
interface ExtensionInterface
{
    public function register(Engine $engine);
}
