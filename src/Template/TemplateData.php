<?php

/**
 * This file is part of mobicms/render library
 *
 * @see     https://github.com/mobicms/render For the canonical source repository
 * @license https://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mobicms\Render\Template;

/**
 * Preassigned template data
 */
class TemplateData
{
    /**
     * Variables shared by all templates
     *
     * @var array<mixed>
     */
    protected array $sharedVariables = [];

    /**
     * Specific template variables
     *
     * @var array<mixed>
     */
    protected array $templateVariables = [];

    /**
     * Add template data
     *
     * @param array<mixed> $data
     * @param array<string> $templates
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
     * @param array<mixed> $data
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
     * @param array<mixed> $data
     * @param array<string> $templates
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
     * @return array<mixed>
     */
    public function get(?string $template = null): array
    {
        if (isset($template, $this->templateVariables[$template])) {
            return array_merge($this->sharedVariables, $this->templateVariables[$template]);
        }

        return $this->sharedVariables;
    }
}
