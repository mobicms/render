<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace Mobicms\Render\Template;

use Mobicms\Render\ExtensionInterface;
use LogicException;

/**
 * A template function
 */
class Func
{
    /** @var string The function name */
    protected $name;

    /** @var callable The function callback */
    protected $callback;

    public function __construct(string $name, callable $callback)
    {
        $this->setName($name);
        $this->setCallback($callback);
    }

    /**
     * Set the function name
     *
     * @param string $name
     * @return Func
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
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the function callback
     *
     * @param callable|null $callback
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
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Call the function
     *
     * @param Template $template
     * @param array $arguments
     * @return mixed
     */
    public function call(Template $template = null, array $arguments = [])
    {
        if (
            is_array($this->callback)
            && isset($this->callback[0])
            && $this->callback[0] instanceof ExtensionInterface
        ) {
            $this->callback[0]->template = $template;
        }

        return call_user_func_array($this->callback, $arguments);
    }
}
