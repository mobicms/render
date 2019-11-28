<?php

declare(strict_types=1);

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

namespace League\Plates\Template;

/**
 * Template file extension.
 */
class FileExtension
{
    /**
     * Template file extension.
     * @var string
     */
    protected $fileExtension;

    /**
     * Create new FileExtension instance.
     * @param null|string $fileExtension
     */
    public function __construct($fileExtension = 'php')
    {
        $this->set($fileExtension);
    }

    /**
     * Set the template file extension.
     * @param  null|string   $fileExtension
     * @return FileExtension
     */
    public function set($fileExtension)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Get the template file extension.
     * @return string
     */
    public function get()
    {
        return $this->fileExtension;
    }
}
