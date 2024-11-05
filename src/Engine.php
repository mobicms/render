<?php

declare(strict_types=1);

namespace Mobicms\Render;

use InvalidArgumentException;
use Mobicms\Render\Template\Template;
use Mobicms\Render\Template\TemplateData;
use Throwable;

use function in_array;

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
     * @var array<callable>
     */
    protected array $functions = [];

    /**
     * Collection of preassigned template data
     */
    protected TemplateData $templateData;

    public function __construct(string $fileExtension = 'phtml')
    {
        $this->fileExtension = $fileExtension;
        $this->templateData = new TemplateData();
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
    public function addPath(string $folder, string $nameSpace = 'main'): self
    {
        if (empty($folder)) {
            throw new InvalidArgumentException('You must specify folder.');
        }

        if (empty($nameSpace)) {
            throw new InvalidArgumentException('Namespace cannot be empty.');
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
     * @return array<string>
     */
    public function getPath(string $name): array
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
     */
    public function addData(array $data, array $templates = []): self
    {
        $this->templateData->add($data, $templates);
        return $this;
    }

    /**
     * Get all preassigned template data
     *
     * @return array<mixed>
     */
    public function getTemplateData(?string $template = null): array
    {
        return $this->templateData->get($template);
    }

    public function registerFunction(string $name, callable $callback): self
    {
        if (isset($this->functions[$name])) {
            throw new InvalidArgumentException('The template function name "' . $name . '" is already registered.');
        }

        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name) !== 1) {
            throw new InvalidArgumentException(
                'Not a valid function name.'
            );
        }

        $this->functions[$name] = $callback;
        return $this;
    }

    public function getFunction(string $name): callable
    {
        if (! isset($this->functions[$name])) {
            throw new InvalidArgumentException('The template function "' . $name . '" was not found.');
        }

        return $this->functions[$name];
    }

    public function doesFunctionExist(string $name): bool
    {
        return isset($this->functions[$name]);
    }

    public function loadExtension(ExtensionInterface $extension): self
    {
        $extension->register($this);
        return $this;
    }

    /**
     * Create a new template and render it
     *
     * @param array<mixed> $params
     * @throws Throwable
     */
    public function render(string $name, array $params = []): string
    {
        $template = new Template($this, $name);
        return $template->render($params);
    }
}
