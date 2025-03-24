<?php

declare(strict_types=1);

namespace Mobicms\Render\Template;

/**
 * Preassigned template data
 */
final class TemplateData
{
    /** @var array<mixed> */
    protected array $sharedVariables = [];

    /** @var array<array-key, mixed> */
    protected array $templateVariables = [];

    /**
     * Add template data
     *
     * @param array<mixed> $data
     * @param array<string> $templates
     */
    public function add(array $data, array $templates = []): self
    {
        /** @phpstan-ignore empty.notAllowed */
        return empty($templates)
            ? $this->shareWithAll($data)
            : $this->shareWithSome($data, $templates);
    }

    /**
     * Add data shared with all templates
     *
     * @param array<mixed> $data
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
     *
     * @return array<mixed>
     */
    public function get(?string $template = null): array
    {
        if (null !== $template && isset($this->templateVariables[$template])) {
            return array_merge($this->sharedVariables, (array) $this->templateVariables[$template]);
        }

        return $this->sharedVariables;
    }
}
