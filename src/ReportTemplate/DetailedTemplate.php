<?php

namespace App\ReportTemplate;

use App\Service\ReportAggregationService;
use Twig\Environment;

class DetailedTemplate extends AbstractTwigTemplate
{
    public function __construct(
        Environment $twig,
        private ReportAggregationService $aggregationService
    ) {
        parent::__construct($twig, 'reports/detailed.html.twig');
    }

    public function getName(): string
    {
        return 'detailed';
    }

    public function getLabel(): string
    {
        return 'Detailed (Individual Entries)';
    }

    protected function prepareData(array $timeEntries, array $context): array
    {
        $aggregatedData = $this->aggregationService->aggregateByProjectAndDay($timeEntries);
        $projectSummary = $this->aggregationService->aggregateByProject($timeEntries);

        return [
            'month' => $context['month'] ?? '',
            'start_date' => $context['start_date'] ?? '',
            'end_date' => $context['end_date'] ?? '',
            'entries' => $aggregatedData,
            'summary' => $projectSummary,
            'total_hours' => array_sum(array_column($projectSummary, 'total_hours')),
        ];
    }
}
