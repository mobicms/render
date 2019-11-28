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
 * Template file extension
 */
class FileExtension
{
    /** @var string Template file extension */
    protected $fileExtension;

    public function __construct(?string $fileExtension = 'php')
    {
        $this->set($fileExtension);
    }

    /**
     * Set the template file extension
     *
     * @param null|string $fileExtension
     * @return FileExtension
     */
    public function set(?string $fileExtension) : self
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Get the template file extension
     *
     * @return string|null
     */
    public function get() : ?string
    {
        return $this->fileExtension;
    }
}
