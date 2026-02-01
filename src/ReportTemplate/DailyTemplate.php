<?php

namespace App\ReportTemplate;

class DailyTemplate implements ReportTemplateInterface
{
    public function getName(): string
    {
        return 'daily';
    }

    public function getLabel(): string
    {
        return 'Daily Summary (with weekends)';
    }

    public function generateHtml(array $timeEntries, array $context = []): string
    {
        $startDate = new \DateTime($context['start_date'] ?? 'first day of this month');
        $endDate = new \DateTime($context['end_date'] ?? 'last day of this month');

        // Aggregate time entries by date
        $dailyData = $this->aggregateByDay($timeEntries, $startDate, $endDate);

        // Build HTML table
        $html = '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Date</th>';
        $html .= '<th>Day</th>';
        $html .= '<th>Total Hours</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('l');
            $isWeekend = in_array($currentDate->format('N'), ['6', '7']); // Saturday or Sunday
            
            $hours = $dailyData[$dateKey] ?? 0;

            // Add data-weekend attribute for weekend rows (will be styled in Excel)
            $rowClass = $isWeekend ? ' class="weekend"' : '';
            
            $html .= "<tr{$rowClass}>";
            $html .= '<td>' . $currentDate->format('Y-m-d') . '</td>';
            $html .= '<td>' . $dayOfWeek . '</td>';
            $html .= '<td>' . number_format($hours, 2) . '</td>';
            $html .= '</tr>';

            $currentDate->modify('+1 day');
        }

        $html .= '</tbody>';
        $html .= '<tfoot>';
        $html .= '<tr>';
        $html .= '<td colspan="2"><strong>Total</strong></td>';
        $html .= '<td><strong>' . number_format(array_sum($dailyData), 2) . '</strong></td>';
        $html .= '</tr>';
        $html .= '</tfoot>';
        $html .= '</table>';

        return $html;
    }

    private function aggregateByDay(array $timeEntries, \DateTime $startDate, \DateTime $endDate): array
    {
        $dailyData = [];

        foreach ($timeEntries as $entry) {
            if (!isset($entry['start']) || !isset($entry['duration'])) {
                continue;
            }

            $entryDate = new \DateTime($entry['start']);
            $dateKey = $entryDate->format('Y-m-d');

            // Only include entries within the date range
            if ($entryDate >= $startDate && $entryDate <= $endDate) {
                if (!isset($dailyData[$dateKey])) {
                    $dailyData[$dateKey] = 0;
                }

                $durationSeconds = $entry['duration'];
                if ($durationSeconds > 0) {
                    $dailyData[$dateKey] += $durationSeconds / 3600; // Convert to hours
                }
            }
        }

        return $dailyData;
    }
}
