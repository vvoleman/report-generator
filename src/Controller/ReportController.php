<?php

namespace App\Controller;

use App\Service\ReportAggregationService;
use App\Service\ReportGeneratorService;
use App\Service\TogglApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    public function __construct(
        private TogglApiService $togglApi,
        private ReportAggregationService $aggregationService,
        private ReportGeneratorService $generatorService
    ) {
    }

    #[Route('/reports', name: 'app_reports')]
    public function index(): Response
    {
        $user = $this->getUser();
        $togglToken = $user ? $user->getActiveTogglToken() : null;

        if (!$togglToken) {
            $this->addFlash('error', 'Please set your Toggl API token in settings first.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('reports/index.html.twig');
    }

    #[Route('/reports/generate', name: 'app_reports_generate', methods: ['POST'])]
    public function generate(Request $request): Response
    {
        $user = $this->getUser();
        $togglToken = $user->getActiveTogglToken();

        if (!$togglToken) {
            $this->addFlash('error', 'Please set your Toggl API token in settings first.');
            return $this->redirectToRoute('app_dashboard');
        }

        $month = $request->request->get('month');
        $template = $request->request->get('template', 'default');

        if (!$month) {
            $this->addFlash('error', 'Please select a month.');
            return $this->redirectToRoute('app_reports');
        }

        try {
            $date = new \DateTime($month . '-01');
            $startDate = new \DateTime($date->format('Y-m-01'));
            $endDate = new \DateTime($date->format('Y-m-t'));

            $timeEntries = $this->togglApi->getTimeEntries(
                $togglToken->getApiToken(),
                $startDate,
                $endDate
            );

            $aggregatedData = $this->aggregationService->aggregateByProjectAndDay($timeEntries);
            $projectSummary = $this->aggregationService->aggregateByProject($timeEntries);

            $reportData = [
                'month' => $date->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'entries' => $aggregatedData,
                'summary' => $projectSummary,
                'total_hours' => array_sum(array_column($projectSummary, 'total_hours')),
            ];

            $filePath = $this->generatorService->generateXlsx($reportData, $template);

            $response = new BinaryFileResponse($filePath);
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="toggl_report_' . $date->format('Y_m') . '.xlsx"');
            $response->deleteFileAfterSend(true);

            return $response;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error generating report: ' . $e->getMessage());
            return $this->redirectToRoute('app_reports');
        }
    }
}
