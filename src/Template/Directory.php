<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace League\Plates\Template;

use LogicException;

/**
 * Default template directory.
 */
class Directory
{
    /**
     * Template directory path.
     * @var string
     */
    protected $path;

    /**
     * Create new Directory instance.
     * @param string $path
     */
    public function __construct($path = null)
    {
        $this->set($path);
    }

    /**
     * Set path to templates directory.
     * @param  string|null $path Pass null to disable the default directory.
     * @return Directory
     */
    public function set($path)
    {
        if (null !== $path && ! is_dir($path)) {
            throw new LogicException(
                'The specified path "' . $path . '" does not exist.'
            );
        }

        $this->path = $path;

        return $this;
    }

    /**
     * Get path to templates directory.
     * @return string
     */
    public function get()
    {
        return $this->path;
    }
}
