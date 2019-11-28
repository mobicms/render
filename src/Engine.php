<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace League\Plates;

use League\Plates\Template\Data;
use League\Plates\Template\Directory;
use League\Plates\Template\FileExtension;
use League\Plates\Template\Folders;
use League\Plates\Template\Func;
use League\Plates\Template\Functions;
use League\Plates\Template\Name;
use League\Plates\Template\Template;

/**
 * Template API and environment settings storage
 */
class Engine
{
    /** @var Directory Default template directory */
    protected $directory;

    /** @var FileExtension Template file extension */
    protected $fileExtension;

    /** @var Folders Collection of template folders */
    protected $folders;

    /** @var Functions Collection of template functions */
    protected $functions;

    /** @var Data Collection of preassigned template data */
    protected $data;

    public function __construct(string $directory = null, string $fileExtension = 'php')
    {
        $this->directory = new Directory($directory);
        $this->fileExtension = new FileExtension($fileExtension);
        $this->folders = new Folders();
        $this->functions = new Functions();
        $this->data = new Data();
    }

    /**
     * Set path to templates directory
     *
     * @param string|null $directory Pass null to disable the default directory
     * @return Engine
     */
    public function setDirectory(?string $directory) : self
    {
        $this->directory->set($directory);

        return $this;
    }

    /**
     * Get path to templates directory
     *
     * @return string|null
     */
    public function getDirectory() : ?string
    {
        return $this->directory->get();
    }

    /**
     * Set the template file extension
     *
     * @param string|null $fileExtension Pass null to manually set it
     * @return Engine
     */
    public function setFileExtension(?string $fileExtension) : self
    {
        $this->fileExtension->set($fileExtension);

        return $this;
    }

    /**
     * Get the template file extension.
     *
     * @return string|null
     */
    public function getFileExtension() : ?string
    {
        return $this->fileExtension->get();
    }

    /**
     * Add a new template folder for grouping templates under different namespaces
     *
     * @param string $name
     * @param string $directory
     * @param bool   $fallback
     * @return Engine
     */
    public function addFolder(string $name, string $directory, bool $fallback = false) : self
    {
        $this->folders->add($name, $directory, $fallback);

        return $this;
    }

    /**
     * Remove a template folder
     *
     * @param string $name
     * @return Engine
     */
    public function removeFolder(string $name) : self
    {
        $this->folders->remove($name);

        return $this;
    }

    /**
     * Get collection of all template folders
     *
     * @return Folders
     */
    public function getFolders() : Folders
    {
        return $this->folders;
    }

    /**
     * Add preassigned template data
     *
     * @param array             $data
     * @param null|string|array $templates
     * @return Engine
     */
    public function addData(array $data, $templates = null) : self
    {
        $this->data->add($data, $templates);

        return $this;
    }

    /**
     * Get all preassigned template data
     *
     * @param null|string $template
     * @return array
     */
    public function getData(?string $template = null) : array
    {
        return $this->data->get($template);
    }

    /**
     * Register a new template function
     *
     * @param string   $name
     * @param callable $callback
     * @return Engine
     */
    public function registerFunction(string $name, callable $callback) : self
    {
        $this->functions->add($name, $callback);

        return $this;
    }

    /**
     * Remove a template function
     *
     * @param string $name
     * @return Engine
     */
    public function dropFunction(string $name) : self
    {
        $this->functions->remove($name);

        return $this;
    }

    /**
     * Get a template function
     *
     * @param string $name
     * @return Func
     */
    public function getFunction(string $name) : Func
    {
        return $this->functions->get($name);
    }

    /**
     * Check if a template function exists
     *
     * @param string $name
     * @return bool
     */
    public function doesFunctionExist(string $name) : bool
    {
        return $this->functions->exists($name);
    }

    /**
     * Load an extension
     *
     * @param ExtensionInterface $extension
     * @return Engine
     */
    public function loadExtension(ExtensionInterface $extension) : self
    {
        $extension->register($this);

        return $this;
    }

    /**
     * Load multiple extensions
     *
     * @param array $extensions
     * @return Engine
     */
    public function loadExtensions(array $extensions = []) : self
    {
        foreach ($extensions as $extension) {
            $this->loadExtension($extension);
        }

        return $this;
    }

    /**
     * Get a template path
     *
     * @param string $name
     * @return string
     */
    public function path(string $name) : string
    {
        $name = new Name($this, $name);

        return $name->getPath();
    }

    /**
     * Check if a template exists
     *
     * @param string $name
     * @return bool
     */
    public function exists(string $name) : bool
    {
        $name = new Name($this, $name);

        return $name->doesPathExist();
    }

    /**
     * Create a new template
     *
     * @param string $name
     * @return Template
     */
    public function make(string $name) : Template
    {
        return new Template($this, $name);
    }

    /**
     * Create a new template and render it
     *
     * @param string $name
     * @param array  $data
     * @return string
     * @throws \Throwable
     */
    public function render(string $name, array $data = []) : string
    {
        return $this->make($name)->render($data);
    }
}
