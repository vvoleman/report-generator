<?php

namespace App\ReportTemplate;

use App\Service\ReportAggregationService;
use Twig\Environment;

class SummaryTemplate extends AbstractTwigTemplate
{
    public function __construct(
        Environment $twig,
        private ReportAggregationService $aggregationService
    ) {
        parent::__construct($twig, 'reports/summary.html.twig');
    }

    public function getName(): string
    {
        return 'summary';
    }

    public function getLabel(): string
    {
        return 'Summary (Project Totals)';
    }

    protected function prepareData(array $timeEntries, array $context): array
    {
        $projectSummary = $this->aggregationService->aggregateByProject($timeEntries);

        return [
            'month' => $context['month'] ?? '',
            'start_date' => $context['start_date'] ?? '',
            'end_date' => $context['end_date'] ?? '',
            'summary' => $projectSummary,
            'total_hours' => array_sum(array_column($projectSummary, 'total_hours')),
        ];
    }
}
