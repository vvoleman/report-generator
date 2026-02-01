<?php

namespace App\ReportTemplate;

use Twig\Environment;

abstract class AbstractTwigTemplate implements ReportTemplateInterface
{
    public function __construct(
        protected Environment $twig,
        protected string $templatePath
    ) {
    }

    public function generateHtml(array $timeEntries, array $context = []): string
    {
        $data = $this->prepareData($timeEntries, $context);
        return $this->twig->render($this->templatePath, $data);
    }

    /**
     * Prepare data for the Twig template
     */
    abstract protected function prepareData(array $timeEntries, array $context): array;
}
