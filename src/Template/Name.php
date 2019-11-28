<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace Mobicms\Render\Template;

use Mobicms\Render\Engine;
use LogicException;

/**
 * A template name
 */
class Name
{
    /** @var Engine Instance of the template engine */
    protected $engine;

    /** @var string The original name */
    protected $name;

    /** @var Folder The parsed template folder */
    protected $folder;

    /** @var string The parsed template filename */
    protected $file;

    public function __construct(Engine $engine, string $name)
    {
        $this->setEngine($engine);
        $this->setName($name);
    }

    /**
     * Set the engine
     *
     * @param Engine $engine
     * @return Name
     */
    public function setEngine(Engine $engine) : self
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Get the engine
     *
     * @return Engine
     */
    public function getEngine() : Engine
    {
        return $this->engine;
    }

    /**
     * Set the original name and parse it
     *
     * @param string $name
     * @return Name
     */
    public function setName(string $name) : self
    {
        $this->name = $name;

        $parts = explode('::', $this->name);

        if (count($parts) === 1) {
            $this->setFile($parts[0]);
        } elseif (count($parts) === 2) {
            $this->setFolder($parts[0]);
            $this->setFile($parts[1]);
        } else {
            throw new LogicException(
                'The template name "' . $this->name . '" is not valid. ' .
                'Do not use the folder namespace separator "::" more than once.'
            );
        }

        return $this;
    }

    /**
     * Get the original name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set the parsed template folder
     *
     * @param string $folder
     * @return Name
     */
    public function setFolder(string $folder) : self
    {
        $this->folder = $this->engine->getFolders()->get($folder);

        return $this;
    }

    /**
     * Get the parsed template folder
     *
     * @return Folder|null
     */
    public function getFolder() : ?Folder
    {
        return $this->folder;
    }

    /**
     * Set the parsed template file
     *
     * @param string $file
     * @return Name
     */
    public function setFile(string $file) : self
    {
        if ($file === '') {
            throw new LogicException(
                'The template name "' . $this->name .
                '" is not valid. The template name cannot be empty.'
            );
        }

        $this->file = $file;

        if (null !== $this->engine->getFileExtension()) {
            $this->file .= '.' . $this->engine->getFileExtension();
        }

        return $this;
    }

    /**
     * Get the parsed template file
     *
     * @return string
     */
    public function getFile() : string
    {
        return $this->file;
    }

    /**
     * Resolve template path
     *
     * @return string
     */
    public function getPath() : string
    {
        if (null === $this->folder) {
            throw new LogicException('The template name "' . $this->name . '" is not valid.');
        }

        $path = $this->folder->getPath() . DIRECTORY_SEPARATOR . $this->file;

        if (! is_file($path)) {
            throw new LogicException('The template name "' . $this->name . '" is not valid.');
        }

        return $path;
    }

    /**
     * Check if template path exists
     *
     * @return bool
     */
    public function doesPathExist() : bool
    {
        return is_file($this->getPath());
    }
}
