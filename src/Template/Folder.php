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
 * A template folder
 */
class Folder
{
    /** @var string The folder name */
    protected $name;

    /** @var string The folder path */
    protected $path;

    /** @var bool The folder fallback status */
    protected $fallback;

    /**
     * Create a new Folder instance
     *
     * @param string $name
     * @param string $path
     * @param bool $fallback
     */
    public function __construct(string $name, string $path, bool $fallback = false)
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setFallback($fallback);
    }

    /**
     * Set the folder name
     *
     * @param string $name
     * @return Folder
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the folder name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the folder path
     *
     * @param string $path
     * @return Folder
     */
    public function setPath(string $path): self
    {
        if (! is_dir($path)) {
            throw new LogicException('The specified directory path "' . $path . '" does not exist.');
        }

        $this->path = $path;

        return $this;
    }

    /**
     * Get the folder path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the folder fallback status
     *
     * @param bool $fallback
     * @return Folder
     */
    public function setFallback(bool $fallback): self
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * Get the folder fallback status
     *
     * @return bool
     */
    public function getFallback(): bool
    {
        return $this->fallback;
    }
}
