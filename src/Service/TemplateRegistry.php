<?php

namespace App\Service;

use App\ReportTemplate\ReportTemplateInterface;

class TemplateRegistry
{
    /** @var ReportTemplateInterface[] */
    private array $templates = [];

    public function __construct(iterable $templates)
    {
        foreach ($templates as $template) {
            $this->addTemplate($template);
        }
    }

    public function addTemplate(ReportTemplateInterface $template): void
    {
        $this->templates[$template->getName()] = $template;
    }

    public function getTemplate(string $name): ?ReportTemplateInterface
    {
        return $this->templates[$name] ?? null;
    }

    public function getAllTemplates(): array
    {
        return $this->templates;
    }

    public function getTemplateChoices(): array
    {
        $choices = [];
        foreach ($this->templates as $template) {
            $choices[$template->getLabel()] = $template->getName();
        }
        return $choices;
    }
}
