<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace Mobicms\Render;

use Mobicms\Render\Template\Data;
use Mobicms\Render\Template\Folders;
use Mobicms\Render\Template\Func;
use Mobicms\Render\Template\Functions;
use Mobicms\Render\Template\Template;

/**
 * Template API and environment settings storage
 */
class Engine
{
    /** @var string Template file extension */
    protected $fileExtension = 'php';

    /** @var Folders Collection of template folders */
    protected $folders;

    /** @var Functions Collection of template functions */
    protected $functions;

    /** @var Data Collection of preassigned template data */
    protected $data;

    public function __construct()
    {
        $this->folders = new Folders();
        $this->functions = new Functions();
        $this->data = new Data();
    }

    /**
     * Set the template file extension
     *
     * @param string $fileExtension
     * @return Engine
     */
    public function setFileExtension(string $fileExtension): self
    {
        $this->fileExtension = $fileExtension;
        return $this;
    }

    /**
     * Get the template file extension.
     *
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * Add a new template folder for grouping templates under different namespaces
     *
     * @param string $name
     * @param string $directory
     * @param bool $fallback
     * @return Engine
     */
    public function addFolder(string $name, string $directory, bool $fallback = false): self
    {
        $this->folders->add($name, $directory, $fallback);
        return $this;
    }

    /**
     * Get collection of all template folders
     *
     * @return Folders
     */
    public function getFolders(): Folders
    {
        return $this->folders;
    }

    /**
     * Add preassigned template data
     *
     * @param array $data
     * @param null|string|array $templates
     * @return Engine
     */
    public function addData(array $data, $templates = null): self
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
    public function getData(?string $template = null): array
    {
        return $this->data->get($template);
    }

    /**
     * Register a new template function
     *
     * @param string $name
     * @param callable $callback
     * @return Engine
     */
    public function registerFunction(string $name, callable $callback): self
    {
        $this->functions->add($name, $callback);
        return $this;
    }

    /**
     * Get a template function
     *
     * @param string $name
     * @return Func
     */
    public function getFunction(string $name): Func
    {
        return $this->functions->get($name);
    }

    /**
     * Check if a template function exists
     *
     * @param string $name
     * @return bool
     */
    public function doesFunctionExist(string $name): bool
    {
        return $this->functions->exists($name);
    }

    /**
     * Load an extension
     *
     * @param ExtensionInterface $extension
     * @return Engine
     */
    public function loadExtension(ExtensionInterface $extension): self
    {
        $extension->register($this);
        return $this;
    }

    /**
     * Create a new template and render it
     *
     * @param string $name
     * @param array $data
     * @return string
     * @throws \Throwable
     */
    public function render(string $name, array $data = []): string
    {
        return (new Template($this, $name))->render($data);
    }
}
