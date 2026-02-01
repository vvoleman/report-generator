<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Twig\Environment;

class ReportGeneratorService
{
    public function __construct(
        private Environment $twig
    ) {
    }

    public function generateXlsx(array $data, string $templateName = 'default'): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Render template to get structure
        $htmlContent = $this->twig->render("reports/{$templateName}.html.twig", $data);

        // Parse HTML and populate spreadsheet
        $this->parseHtmlToSpreadsheet($htmlContent, $sheet);

        // Write to file
        $tempFile = tempnam(sys_get_temp_dir(), 'toggl_report_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return $tempFile;
    }

    private function parseHtmlToSpreadsheet(string $html, $sheet): void
    {
        // Simple HTML table parser
        $dom = new \DOMDocument();
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $tables = $dom->getElementsByTagName('table');
        
        if ($tables->length === 0) {
            return;
        }

        $table = $tables->item(0);
        $row = 1;

        // Process table headers
        $headers = $table->getElementsByTagName('thead');
        if ($headers->length > 0) {
            $headerRows = $headers->item(0)->getElementsByTagName('tr');
            foreach ($headerRows as $tr) {
                $col = 'A';
                $ths = $tr->getElementsByTagName('th');
                foreach ($ths as $th) {
                    $value = trim($th->textContent);
                    $sheet->setCellValue($col . $row, $value);
                    
                    // Style header
                    $sheet->getStyle($col . $row)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '4472C4']
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ]
                    ]);
                    
                    $col++;
                }
                $row++;
            }
        }

        // Process table body
        $bodies = $table->getElementsByTagName('tbody');
        if ($bodies->length > 0) {
            $bodyRows = $bodies->item(0)->getElementsByTagName('tr');
            foreach ($bodyRows as $tr) {
                $col = 'A';
                $tds = $tr->getElementsByTagName('td');
                foreach ($tds as $td) {
                    $value = trim($td->textContent);
                    
                    // Try to parse as number
                    if (is_numeric($value)) {
                        $sheet->setCellValue($col . $row, (float)$value);
                    } else {
                        $sheet->setCellValue($col . $row, $value);
                    }
                    
                    // Style body cells
                    $sheet->getStyle($col . $row)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ]
                    ]);
                    
                    $col++;
                }
                $row++;
            }
        }

        // Auto-size columns
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
