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
 * A collection of template functions.
 */
class Functions
{
    /**
     * Array of template functions.
     * @var array
     */
    protected $functions = [];

    /**
     * Add a new template function.
     * @param  string    $name;
     * @param  callback  $callback;
     * @return Functions
     */
    public function add($name, $callback)
    {
        if ($this->exists($name)) {
            throw new LogicException(
                'The template function name "' . $name . '" is already registered.'
            );
        }

        $this->functions[$name] = new Func($name, $callback);

        return $this;
    }

    /**
     * Remove a template function.
     * @param  string    $name;
     * @return Functions
     */
    public function remove($name)
    {
        if (! $this->exists($name)) {
            throw new LogicException(
                'The template function "' . $name . '" was not found.'
            );
        }

        unset($this->functions[$name]);

        return $this;
    }

    /**
     * Get a template function.
     * @param  string $name
     * @return Func
     */
    public function get($name)
    {
        if (! $this->exists($name)) {
            throw new LogicException('The template function "' . $name . '" was not found.');
        }

        return $this->functions[$name];
    }

    /**
     * Check if a template function exists.
     * @param  string  $name
     * @return bool
     */
    public function exists($name)
    {
        return isset($this->functions[$name]);
    }
}
