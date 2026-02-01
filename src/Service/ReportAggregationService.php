<?php

namespace App\Service;

class ReportAggregationService
{
    public function aggregateByProjectAndDay(array $timeEntries): array
    {
        $aggregated = [];

        foreach ($timeEntries as $entry) {
            if (!isset($entry['start']) || !isset($entry['duration'])) {
                continue;
            }

            $date = (new \DateTime($entry['start']))->format('Y-m-d');
            $projectId = $entry['project_id'] ?? 'no_project';
            $projectName = $entry['project_name'] ?? 'No Project';
            $description = $entry['description'] ?? '';

            $key = $projectId . '_' . $date;

            if (!isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'date' => $date,
                    'project_id' => $projectId,
                    'project_name' => $projectName,
                    'total_seconds' => 0,
                    'total_hours' => 0,
                    'entries' => [],
                ];
            }

            $durationSeconds = $entry['duration'];
            if ($durationSeconds > 0) {
                $aggregated[$key]['total_seconds'] += $durationSeconds;
                $aggregated[$key]['total_hours'] = round($aggregated[$key]['total_seconds'] / 3600, 2);
                $aggregated[$key]['entries'][] = [
                    'description' => $description,
                    'duration' => $durationSeconds,
                    'hours' => round($durationSeconds / 3600, 2),
                ];
            }
        }

        return array_values($aggregated);
    }

    public function aggregateByProject(array $timeEntries): array
    {
        $aggregated = [];

        foreach ($timeEntries as $entry) {
            if (!isset($entry['start']) || !isset($entry['duration'])) {
                continue;
            }

            $projectId = $entry['project_id'] ?? 'no_project';
            $projectName = $entry['project_name'] ?? 'No Project';

            if (!isset($aggregated[$projectId])) {
                $aggregated[$projectId] = [
                    'project_id' => $projectId,
                    'project_name' => $projectName,
                    'total_seconds' => 0,
                    'total_hours' => 0,
                ];
            }

            $durationSeconds = $entry['duration'];
            if ($durationSeconds > 0) {
                $aggregated[$projectId]['total_seconds'] += $durationSeconds;
                $aggregated[$projectId]['total_hours'] = round($aggregated[$projectId]['total_seconds'] / 3600, 2);
            }
        }

        return array_values($aggregated);
    }
}
