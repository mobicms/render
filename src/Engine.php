<?php

declare(strict_types=1);

namespace Mobicms\Render;

use InvalidArgumentException;
use Mobicms\Render\Template\Template;
use Mobicms\Render\Template\TemplateData;
use Mobicms\Render\Template\TemplateFunction;
use Throwable;

use function in_array;

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
     * @var array<array<string>>
     */
    private array $nameSpaces = [];

    /**
     * Collection of template functions
     *
     * @var array<TemplateFunction>
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
     */
    public function addFolder(string $nameSpace, string $folder): self
    {
        if (empty($nameSpace)) {
            throw new InvalidArgumentException('You must specify namespace.');
        }

        if (empty($folder)) {
            throw new InvalidArgumentException('You must specify folder.');
        }

        $folder = rtrim($folder, '/\\');

        if (
            isset($this->nameSpaces[$nameSpace])
            && in_array($folder, $this->nameSpaces[$nameSpace])
        ) {
            throw new InvalidArgumentException(
                'The "' . $folder . '" folder in the "' . $nameSpace . '" namespace already exists.'
            );
        }

        $this->nameSpaces[$nameSpace][] = $folder;
        return $this;
    }

    /**
     * Get a template folder
     *
     * @param string $name
     * @return array<string>
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
