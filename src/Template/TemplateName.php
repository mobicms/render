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
    /** @var string The original name */
    private $name;

    /** @var array The parsed template folder */
    private $folder;

    /** @var string The parsed template filename */
    private $file;

    public function __construct(Engine $engine, string $name)
    {
        $this->name = $name;
        $parts = explode('::', $this->name);

        if (count($parts) === 2) {
            $this->folder = $engine->getFolder($parts[0]);
            $this->file = $parts[1] . '.' . $engine->getFileExtension();
        } else {
            throw new LogicException(
                'The template name "' . $this->name . '" is not valid. ' .
                'You must use the folder namespace separator "::" once.'
            );
        }
    }

    /**
     * Resolve template path
     *
     * @return string
     */
    public function getPath(): string
    {
        $folderList = array_reverse($this->folder);

        foreach ($folderList as $folder) {
            $path = $folder . DIRECTORY_SEPARATOR . $this->file;

            if (is_file($path)) {
                return $path;
            }
        }

        throw new LogicException('The template "' . $this->name . '" does not exist.');
    }
}
