<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
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
    /**
     * The original name
     */
    private string $name;

    /**
     * The parsed template folder
     *
     * @var array<array-key, string>
     */
    private array $folder;

    /**
     * The parsed template filename
     */
    private string $file;

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
