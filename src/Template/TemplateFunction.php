<?php

declare(strict_types=1);

namespace Mobicms\Render\Template;

use LogicException;

class TemplateFunction
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        if (! is_callable($callback, true)) {
            throw new LogicException(
                'Not a valid function callback.'
            );
        }

        $this->callback = $callback;
    }

    /**
     * @return mixed
     */
    public function call(array $arguments = [])
    {
        return call_user_func_array($this->callback, $arguments);
    }
}
