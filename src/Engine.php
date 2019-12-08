<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace Mobicms\Render;

use LogicException;
use Mobicms\Render\Template\{
    TemplateData,
    Template,
    TemplateFunction
};

/**
 * Template API and environment settings storage
 */
class Engine
{
    /** @var string Template file extension */
    protected $fileExtension;

    /** @var array Collection of template namespaces */
    private $nameSpaces = [];

    /** @var array Collection of template functions */
    protected $functions = [];

    /** @var TemplateData Collection of preassigned template data */
    protected $data;

    /**
     * @param string $fileExtension
     */
    public function __construct(string $fileExtension = 'phtml')
    {
        $this->fileExtension = $fileExtension;
        $this->data = new TemplateData();
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
     * @param string $name Namespace
     * @param string $directory Default (fallback) directory
     * @param array $search Array with a list of folders where templates will be searched
     * @return Engine
     */
    public function addFolder(string $name, string $directory, array $search = []): self
    {
        if (isset($this->nameSpaces[$name])) {
            throw new LogicException('The template namespace "' . $name . '" is already being used.');
        }

        if (! is_dir($directory)) {
            throw new LogicException('The specified directory path "' . $directory . '" does not exist.');
        }

        $this->nameSpaces[$name] = array_merge([$directory], $search);
        return $this;
    }

    /**
     * Get a template folder
     *
     * @param string $name
     * @return array
     */
    public function getFolder(string $name): array
    {
        if (! isset($this->nameSpaces[$name])) {
            throw new LogicException('The template namespace "' . $name . '" was not found.');
        }

        return $this->nameSpaces[$name];
    }

    /**
     * Add preassigned template data
     *
     * @param array $data
     * @param array $templates
     * @return Engine
     */
    public function addData(array $data, array $templates = []): self
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
        if (isset($this->functions[$name])) {
            throw new LogicException('The template function name "' . $name . '" is already registered.');
        }

        $this->functions[$name] = new TemplateFunction($name, $callback);
        return $this;
    }

    /**
     * Get a template function
     *
     * @param string $name
     * @return TemplateFunction
     */
    public function getFunction(string $name): TemplateFunction
    {
        if (! isset($this->functions[$name])) {
            throw new LogicException('The template function "' . $name . '" was not found.');
        }

        return $this->functions[$name];
    }

    /**
     * Check if a template function exists
     *
     * @param string $name
     * @return bool
     */
    public function doesFunctionExist(string $name): bool
    {
        return isset($this->functions[$name]);
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
