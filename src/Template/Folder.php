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
 * A template folder.
 */
class Folder
{
    /**
     * The folder name.
     * @var string
     */
    protected $name;

    /**
     * The folder path.
     * @var string
     */
    protected $path;

    /**
     * The folder fallback status.
     * @var bool
     */
    protected $fallback;

    /**
     * Create a new Folder instance.
     * @param string  $name
     * @param string  $path
     * @param bool $fallback
     */
    public function __construct($name, $path, $fallback = false)
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setFallback($fallback);
    }

    /**
     * Set the folder name.
     * @param  string $name
     * @return Folder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the folder name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the folder path.
     * @param  string $path
     * @return Folder
     */
    public function setPath($path)
    {
        if (! is_dir($path)) {
            throw new LogicException('The specified directory path "' . $path . '" does not exist.');
        }

        $this->path = $path;

        return $this;
    }

    /**
     * Get the folder path.
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the folder fallback status.
     * @param  bool $fallback
     * @return Folder
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * Get the folder fallback status.
     * @return bool
     */
    public function getFallback()
    {
        return $this->fallback;
    }
}
