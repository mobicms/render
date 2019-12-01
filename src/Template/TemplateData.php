<?php

/**
 * This file is part of mobicms/render library
 *
 * @license     https://opensource.org/licenses/MIT MIT (see the LICENSE file)
 * @link        http://mobicms.org mobiCMS Project
 */

declare(strict_types=1);

namespace Mobicms\Render\Template;

/**
 * Preassigned template data
 */
class TemplateData
{
    /** @var array Variables shared by all templates */
    protected $sharedVariables = [];

    /** @var array Specific template variables */
    protected $templateVariables = [];

    /**
     * Add template data
     *
     * @param array $data
     * @param array $templates
     * @return TemplateData
     */
    public function add(array $data, array $templates = []): self
    {
        return empty($templates)
            ? $this->shareWithAll($data)
            : $this->shareWithSome($data, $templates);
    }

    /**
     * Add data shared with all templates
     *
     * @param array $data
     * @return TemplateData
     */
    public function shareWithAll(array $data): self
    {
        $this->sharedVariables = array_merge($this->sharedVariables, $data);

        return $this;
    }

    /**
     * Add data shared with some templates
     *
     * @param array $data
     * @param array $templates
     * @return TemplateData
     */
    public function shareWithSome(array $data, array $templates): self
    {
        foreach ($templates as $template) {
            if (isset($this->templateVariables[$template])) {
                $this->templateVariables[$template] = array_merge($this->templateVariables[$template], $data);
            } else {
                $this->templateVariables[$template] = $data;
            }
        }

        return $this;
    }

    /**
     * Get template data
     *
     * @param null|string $template
     * @return array
     */
    public function get(?string $template = null): array
    {
        if (isset($template, $this->templateVariables[$template])) {
            return array_merge($this->sharedVariables, $this->templateVariables[$template]);
        }

        return $this->sharedVariables;
    }
}
