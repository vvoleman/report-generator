<?php

namespace App\ReportTemplate;

interface ReportTemplateInterface
{
    /**
     * Get the unique name/identifier for this template
     */
    public function getName(): string;

    /**
     * Get the display label for this template
     */
    public function getLabel(): string;

    /**
     * Generate HTML table from time entries data
     * 
     * @param array $timeEntries Raw time entries from Toggl API
     * @param array $context Additional context (start_date, end_date, etc.)
     * @return string HTML table content
     */
    public function generateHtml(array $timeEntries, array $context = []): string;
}
