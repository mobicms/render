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

    /**
     * Create a new Folder instance
     *
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path)
    {
        $this->setName($name);
        $this->setPath($path);
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
}
