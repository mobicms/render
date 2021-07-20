<?php

declare(strict_types=1);

namespace Mobicms\Render\Template;

use LogicException;

/**
 * A template function
 */
class TemplateFunction
{
    /**
     * The function name
     */
    protected string $name;

    /**
     * The function callback
     *
     * @var callable
     */
    protected $callback;

    public function __construct(string $name, callable $callback)
    {
        $this->setName($name);
        $this->setCallback($callback);
    }

    /**
     * Set the function name
     *
     * @return TemplateFunction
     */
    public function setName(string $name): self
    {
        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name) !== 1) {
            throw new LogicException(
                'Not a valid function name.'
            );
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Get the function name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the function callback
     *
     * @return $this
     */
    public function setCallback(?callable $callback): self
    {
        if (! is_callable($callback, true)) {
            throw new LogicException(
                'Not a valid function callback.'
            );
        }

        $this->callback = $callback;

        return $this;
    }

    /**
     * Get the function callback
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Call the function
     *
     * @param array<mixed> $arguments
     * @return mixed
     */
    public function call(array $arguments = [])
    {
        return call_user_func_array($this->callback, $arguments);
    }
}
