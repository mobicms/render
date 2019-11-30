<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace Mobicms\Render\Template;

use Mobicms\Render\Engine;
use LogicException;

/**
 * A template name
 */
class TemplateName
{
    /** @var Engine Instance of the template engine */
    private $engine;

    /** @var string The original name */
    private $name;

    /** @var array The parsed template folder */
    private $folder;

    /** @var string The parsed template filename */
    private $file;

    public function __construct(Engine $engine, string $name)
    {
        $this->engine = $engine;
        $this->name = $name;
        $this->parseTemplateName();
    }

    /**
     * Resolve template path
     *
     * @return string
     */
    public function getPath(): string
    {
        $path = $this->folder['directory'] . DIRECTORY_SEPARATOR . $this->file;

        if (! is_file($path)) {
            throw new LogicException('The template name "' . $this->name . '" is not valid.');
        }

        return $path;
    }

    /**
     * Set the original name and parse it
     */
    private function parseTemplateName(): void
    {
        $parts = explode('::', $this->name);

        if (count($parts) === 2) {
            $this->folder = $this->engine->getFolder($parts[0]);
            $this->file = $parts[1] . '.' . $this->engine->getFileExtension();
        } else {
            throw new LogicException(
                'The template name "' . $this->name . '" is not valid. ' .
                'Do not use the folder namespace separator "::" more than once.'
            );
        }
    }
}
