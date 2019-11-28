<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace League\Plates\Template;

use LogicException;

/**
 * A collection of template folders.
 */
class Folders
{
    /**
     * Array of template folders.
     * @var array
     */
    protected $folders = [];

    /**
     * Add a template folder.
     * @param  string  $name
     * @param  string  $path
     * @param  bool $fallback
     * @return Folders
     */
    public function add($name, $path, $fallback = false)
    {
        if ($this->exists($name)) {
            throw new LogicException('The template folder "' . $name . '" is already being used.');
        }

        $this->folders[$name] = new Folder($name, $path, $fallback);

        return $this;
    }

    /**
     * Remove a template folder.
     * @param  string  $name
     * @return Folders
     */
    public function remove($name)
    {
        if (! $this->exists($name)) {
            throw new LogicException('The template folder "' . $name . '" was not found.');
        }

        unset($this->folders[$name]);

        return $this;
    }

    /**
     * Get a template folder.
     * @param  string $name
     * @return Folder
     */
    public function get($name)
    {
        if (! $this->exists($name)) {
            throw new LogicException('The template folder "' . $name . '" was not found.');
        }

        return $this->folders[$name];
    }

    /**
     * Check if a template folder exists.
     * @param  string  $name
     * @return bool
     */
    public function exists($name)
    {
        return isset($this->folders[$name]);
    }
}
