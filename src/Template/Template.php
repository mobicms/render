<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace League\Plates\Template;

use League\Plates\Engine;
use LogicException;
use Throwable;

/**
 * Container which holds template data and provides access to template functions
 */
class Template
{
    /**
     * Instance of the template engine
     *
     * @var Engine
     */
    protected $engine;

    /**
     * The name of the template
     *
     * @var Name
     */
    protected $name;

    /**
     * The data assigned to the template
     *
     * @var array
     */
    protected $data = [];

    /**
     * An array of section content
     *
     * @var array
     */
    protected $sections = [];

    /**
     * The name of the section currently being rendered
     *
     * @var string
     */
    protected $sectionName;

    /**
     * Whether the section should be appended or not
     *
     * @var bool
     */
    protected $appendSection;

    /**
     * The name of the template layout
     *
     * @var string
     */
    protected $layoutName;

    /**
     * The data assigned to the template layout
     *
     * @var array
     */
    protected $layoutData;

    public function __construct(Engine $engine, string $name)
    {
        $this->engine = $engine;
        $this->name = new Name($engine, $name);

        $this->data($this->engine->getData($name));
    }

    /**
     * Magic method used to call extension functions
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->engine->getFunction($name)->call($this, $arguments);
    }

    /**
     * Alias for render() method
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function __toString() : string
    {
        return $this->render();
    }

    /**
     * Assign or get template data
     *
     * @param array $data
     * @return array
     */
    public function data(array $data = []) : array
    {
        $this->data = array_merge($this->data, $data);

        return $this->data;
    }

    /**
     * Check if the template exists
     *
     * @return bool
     */
    public function exists() : bool
    {
        return $this->name->doesPathExist();
    }

    /**
     * Get the template path
     *
     * @return string
     */
    public function path() : string
    {
        return $this->name->getPath();
    }

    /**
     * Render the template and layout
     *
     * @param array $data
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function render(array $data = []) : string
    {
        $this->data($data);
        unset($data);
        extract($this->data);

        if (! $this->exists()) {
            throw new LogicException(
                'The template "' . $this->name->getName() . '" could not be found at "' . $this->path() . '".'
            );
        }

        try {
            $level = ob_get_level();
            ob_start();

            include $this->path();

            $content = ob_get_clean();

            if (isset($this->layoutName)) {
                $layout = $this->engine->make($this->layoutName);
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
     * @param string $name
     * @param array  $data
     */
    public function layout(string $name, array $data = []) : void
    {
        $this->layoutName = $name;
        $this->layoutData = $data;
    }

    /**
     * Start a new section block
     *
     * @param string $name
     */
    public function start(string $name) : void
    {
        if ($name === 'content') {
            throw new LogicException(
                'The section name "content" is reserved.'
            );
        }

        if ($this->sectionName) {
            throw new LogicException('You cannot nest sections within other sections.');
        }

        $this->sectionName = $name;

        ob_start();
    }

    /**
     * Start a new append section block
     *
     * @param string $name
     */
    public function push(string $name) : void
    {
        $this->appendSection = true;
        $this->start($name);
    }

    /**
     * Stop the current section block
     */
    public function stop() : void
    {
        if (null === $this->sectionName) {
            throw new LogicException(
                'You must start a section before you can stop it.'
            );
        }

        if (! isset($this->sections[$this->sectionName])) {
            $this->sections[$this->sectionName] = '';
        }

        $this->sections[$this->sectionName] = $this->appendSection
            ? $this->sections[$this->sectionName] . ob_get_clean()
            : ob_get_clean();
        $this->sectionName = null;
        $this->appendSection = false;
    }

    /**
     * Alias of stop()
     */
    public function end() : void
    {
        $this->stop();
    }

    /**
     * Returns the content for a section block
     *
     * @param string $name    Section name
     * @param string $default Default section content
     * @return string|null
     */
    public function section(string $name, string $default = null) : ?string
    {
        if (! isset($this->sections[$name])) {
            return $default;
        }

        return $this->sections[$name];
    }

    /**
     * Fetch a rendered template
     *
     * @param string $name
     * @param array  $data
     * @return string
     * @throws Throwable
     */
    public function fetch(string $name, array $data = []) : string
    {
        return $this->engine->render($name, $data);
    }

    /**
     * Output a rendered template
     *
     * @param string $name
     * @param array  $data
     * @throws Throwable
     */
    public function insert(string $name, array $data = []) : void
    {
        echo $this->engine->render($name, $data);
    }

    /**
     * Apply multiple functions to variable
     *
     * @param mixed  $var
     * @param string $functions
     * @return mixed
     */
    public function batch($var, string $functions)
    {
        foreach (explode('|', $functions) as $function) {
            if ($this->engine->doesFunctionExist($function)) {
                $var = call_user_func([$this, $function], $var);
            } elseif (is_callable($function)) {
                $var = call_user_func($function, $var);
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
     *
     * @param string      $string
     * @param null|string $functions
     * @return string
     */
    public function escape(string $string, ?string $functions = null) : string
    {
        static $flags;

        if (! isset($flags)) {
            $flags = ENT_QUOTES | (defined('ENT_SUBSTITUTE') ? ENT_SUBSTITUTE : 0);
        }

        if ($functions) {
            $string = $this->batch($string, $functions);
        }

        return htmlspecialchars($string, $flags, 'UTF-8');
    }

    /**
     * Alias to escape function
     *
     * @param string      $string
     * @param null|string $functions
     * @return string
     */
    public function e(string $string, ?string $functions = null) : string
    {
        return $this->escape($string, $functions);
    }
}
