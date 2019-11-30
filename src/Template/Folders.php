<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace Mobicms\Render\Template;

use LogicException;

/**
 * A collection of template folders
 */
class Folders
{
    /** @var array Array of template folders */
    protected $folders = [];

    /**
     * Add a template folder
     *
     * @param string $name
     * @param string $path
     * @return Folders
     */
    public function add(string $name, string $path): self
    {
        if (isset($this->folders[$name])) {
            throw new LogicException('The template folder "' . $name . '" is already being used.');
        }

        $this->folders[$name] = new Folder($name, $path);
        return $this;
    }

    /**
     * Get a template folder
     *
     * @param string $name
     * @return Folder
     */
    public function get(string $name): Folder
    {
        if (! isset($this->folders[$name])) {
            throw new LogicException('The template folder "' . $name . '" was not found.');
        }

        return $this->folders[$name];
    }
}
