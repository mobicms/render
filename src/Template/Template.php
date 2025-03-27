<?php

declare(strict_types=1);

namespace Mobicms\Render\Template;

use LogicException;
use Mobicms\Render\Engine;
use Throwable;

/**
 * Container which holds template data and provides access to template functions
 *
 * @psalm-api
 */
final class Template
{
    /**
     * @var array<string>
     */
    private array $sections = [];

    private Engine $engine;

    private string $templateFile;

    /** @var array<mixed> */
    private array $data = [];

    private string $sectionName = '';

    private bool $appendSection = false;

    private string $layoutName = '';

    /** @var array<mixed> */
    private array $layoutData = [];

    public function __construct(Engine $engine, string $name)
    {
        $this->engine = $engine;
        $templateName = new TemplateName($engine, $name);
        $this->templateFile = $templateName->resolvePath();
        $this->data($this->engine->getTemplateData($name));
    }

    /**
     * Magic method used to call extension functions
     *
     * @param array<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return call_user_func_array($this->engine->getFunction($name), $arguments);
    }

    /**
     * Alias for render() method
     *
     * @throws Throwable
     * @throws \Exception
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Assign or get template data
     *
     * @param array<mixed> $data
     * @return array<mixed>
     */
    public function data(array $data = []): array
    {
        $this->data = array_merge($this->data, $data);

        return $this->data;
    }

    /**
     * Render the template and layout
     *
     * @param array<mixed> $data
     * @throws Throwable
     *
     * @psalm-suppress UnusedParam
     */
    public function render(array $data = []): string
    {
        extract($this->data($data), EXTR_SKIP);

        try {
            $level = ob_get_level();
            ob_start();
            /** @psalm-suppress UnresolvableInclude */
            include $this->templateFile; //NOSONAR
            $content = (string) ob_get_clean();

            if ($this->layoutName !== '') {
                $layout = new self($this->engine, $this->layoutName);
                $layout->sections = array_merge($this->sections, ['content' => $content]);
                $content = $layout->render($this->layoutData);
            }

            return $content;
        } catch (Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }

    /**
     * Set the template's layout
     *
     * @param array<mixed> $data
     */
    public function layout(string $name, array $data = []): void
    {
        $this->layoutName = $name;
        $this->layoutData = $data;
    }

    public function sectionReplace(string $name, string $content): void
    {
        $this->sections[$name] = $content;
    }

    public function sectionAppend(string $name, string $content): void
    {
        if (! isset($this->sections[$name])) {
            $this->sections[$name] = '';
        }

        $this->sections[$name] .= $content;
    }

    /**
     * Start a new section block
     */
    public function start(string $name): void
    {
        if ($name === 'content') {
            throw new LogicException(
                'The section name "content" is reserved.'
            );
        }

        if ($this->sectionName !== '') {
            throw new LogicException('You cannot nest sections within other sections.');
        }

        $this->sectionName = $name;

        ob_start();
    }

    /**
     * Start a new append section block
     */
    public function push(string $name): void
    {
        $this->appendSection = true;
        $this->start($name);
    }

    /**
     * Stop the current section block
     */
    public function stop(): void
    {
        if ($this->sectionName === '') {
            throw new LogicException(
                'You must start a section before you can stop it.'
            );
        }

        if (! isset($this->sections[$this->sectionName])) {
            $this->sections[$this->sectionName] = '';
        }

        $this->sections[$this->sectionName] = $this->appendSection
            ? $this->sections[$this->sectionName] . (string) ob_get_clean()
            : (string) ob_get_clean();
        $this->sectionName = '';
        $this->appendSection = false;
    }

    /**
     * Returns the content for a section block
     */
    public function section(string $name, string $default = ''): string
    {
        if (! isset($this->sections[$name])) {
            return $default;
        }

        return $this->sections[$name];
    }

    /**
     * Fetch a rendered template
     *
     * @param array<mixed> $data
     * @throws Throwable
     */
    public function fetch(string $name, array $data = []): string
    {
        return $this->engine->render($name, $data);
    }

    /**
     * Apply multiple functions to variable
     */
    public function batch(string $var, string $functions): mixed
    {
        foreach (explode('|', $functions) as $function) {
            if ($this->engine->doesFunctionExist($function)) {
                /** @var mixed $var */
                $var = call_user_func([$this, $function], $var);
            } elseif (is_callable($function)) {
                /** @var mixed $var */
                $var = $function($var);
            } else {
                throw new LogicException(
                    'The batch function could not find the "' . $function . '" function.'
                );
            }
        }

        return $var;
    }

    /**
     * Escape string
     */
    public function e(string $string, string $functions = ''): string
    {
        if ('' !== $functions) {
            $string = (string) $this->batch($string, $functions);
        }

        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
