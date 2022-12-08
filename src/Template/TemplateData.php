<?php

declare(strict_types=1);

namespace Mobicms\Render\Template;

/**
 * Preassigned template data
 */
class TemplateData
{
    protected array $sharedVariables = [];

    protected array $templateVariables = [];

    /**
     * Add template data
     *
     * @param array<string> $templates
     */
    public function add(array $data, array $templates = []): self
    {
        return empty($templates)
            ? $this->shareWithAll($data)
            : $this->shareWithSome($data, $templates);
    }

    /**
     * Add data shared with all templates
     */
    public function shareWithAll(array $data): self
    {
        $this->sharedVariables = array_merge($this->sharedVariables, $data);

        return $this;
    }

    /**
     * Add data shared with some templates
     *
     * @param array<string> $templates
     */
    public function shareWithSome(array $data, array $templates): self
    {
        foreach ($templates as $template) {
            if (isset($this->templateVariables[$template])) {
                $this->templateVariables[$template] = array_merge((array) $this->templateVariables[$template], $data);
            } else {
                $this->templateVariables[$template] = $data;
            }
        }

        return $this;
    }

    /**
     * Get template data
     */
    public function get(?string $template = null): array
    {
        if (null !== $template && isset($this->templateVariables[$template])) {
            return array_merge($this->sharedVariables, (array) $this->templateVariables[$template]);
        }

        return $this->sharedVariables;
    }
}
