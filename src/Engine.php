<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mobicms\Render;

use InvalidArgumentException;
use Mobicms\Render\Template\Template;
use Mobicms\Render\Template\TemplateData;
use Mobicms\Render\Template\TemplateFunction;
use Throwable;

/**
 * Template API and environment settings storage
 */
class Engine
{
    /*
     * Template file extension
     */
    protected string $fileExtension;

    /**
     * Collection of template namespaces
     *
     * @var array<array-key, array<array-key, string>>
     */
    private array $nameSpaces = [];

    /**
     * Collection of template functions
     *
     * @var array<array-key, TemplateFunction>
     */
    protected array $functions = [];

    /**
     * Collection of preassigned template data
     */
    protected TemplateData $data;

    public function __construct(string $fileExtension = 'phtml')
    {
        $this->fileExtension = $fileExtension;
        $this->data = new TemplateData();
    }

    /**
     * Get the template file extension
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
     * @param array<string> $search Array with a list of folders where templates will be searched
     * @return Engine
     */
    public function addFolder(string $name, string $directory, array $search = []): self
    {
        $directory = rtrim($directory, '/\\');
        //TODO: После переделки, проверять только совпадение namespace/folder
        if (isset($this->nameSpaces[$name])) {
            throw new InvalidArgumentException('The template namespace "' . $name . '" is already being used.');
        }

        //TODO: Убрать. Проверку существования папки делать при рендеринге конкретного шаблона.
        if (! is_dir($directory)) {
            throw new InvalidArgumentException('The specified directory path "' . $directory . '" does not exist.');
        }

        $this->nameSpaces[$name] = array_merge([$directory], $search);
        return $this;
    }

    /**
     * Get a template folder
     *
     * @param string $name
     * @return array<array-key, string>
     */
    public function getFolder(string $name): array
    {
        if (! isset($this->nameSpaces[$name])) {
            throw new InvalidArgumentException('The template namespace "' . $name . '" was not found.');
        }

        return $this->nameSpaces[$name];
    }

    /**
     * Add preassigned template data
     *
     * @param array<mixed> $data
     * @param array<string> $templates
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
     * @param string|null $template
     * @return array<mixed>
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
            throw new InvalidArgumentException('The template function name "' . $name . '" is already registered.');
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
            throw new InvalidArgumentException('The template function "' . $name . '" was not found.');
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
     * @param array $params
     * @return string
     * @throws Throwable
     */
    public function render(string $name, array $params = []): string
    {
        $template = new Template($this, $name);
        return $template->render($params);
    }
}
