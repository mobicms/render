<?php

declare(strict_types=1);

namespace Mobicms\Render\Template;

use InvalidArgumentException;
use Mobicms\Render\Engine;

class TemplateName
{
    private string $name;

    private array $folder;

    private string $file;

    public function __construct(Engine $engine, string $name)
    {
        $this->name = $name;
        $parts = explode('::', $this->name);
        $count = count($parts);

        if ($count > 2) {
            throw new InvalidArgumentException(
                'The template name "' . $this->name . '" is not valid. ' .
                'You must use the folder namespace separator "::" once.'
            );
        }

        if ($count === 1) {
            array_unshift($parts, 'main');
        }

        $this->folder = $engine->getPath($parts[0]);
        $this->file = $parts[1] . '.' . $engine->getFileExtension();
    }

    public function resolvePath(): string
    {
        $folderList = array_reverse($this->folder);

        /** @var string $folder */
        foreach ($folderList as $folder) {
            $path = $folder . DIRECTORY_SEPARATOR . $this->file;

            if (is_file($path)) {
                return $path;
            }
        }

        throw new InvalidArgumentException('The template "' . $this->name . '" does not exist.');
    }
}
