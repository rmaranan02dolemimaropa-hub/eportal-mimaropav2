<?php

namespace App\Controllers;

use App\Models\HrRequestModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Throwable;


class ExportController extends BaseController
{
    protected HrRequestModel $requestModel;

    public function __construct()
    {
        $this->requestModel = new HrRequestModel();
    }

    public function exportHrRequestReport()
    {
        try {
            $year = $this->request->getGet('year');
            $status = $this->request->getGet('status');
            $serviceAvailed = $this->request->getGet('service_availed');
            $dateRequested = $this->request->getGet('date_requested');

            $builder = $this->requestModel
                ->select('
                reference_no AS reference,
                fullname AS name,
                service_availed AS service,
                status
            ');

            if (!empty($year)) {
                $builder->where('YEAR(date_requested)', $year);
            }

            if (!empty($status)) {
                $builder->where('status', $status);
            }

            if (!empty($serviceAvailed)) {
                $builder->where('service_availed', $serviceAvailed);
            }

            if (!empty($dateRequested)) {
                $builder->where('date_requested', $dateRequested);
            }

            $requests = $builder
                ->orderBy('id', 'DESC')
                ->findAll();

            $html = view('exports/request_report', [
                'title' => 'HR Request Report',
                'requests' => $requests,
                'headerImage' => $this->imageDataUri(
                    FCPATH . 'assets/img/dole-header.png'
                ),
                'footerImage' => $this->imageDataUri(
                    FCPATH . 'assets/img/dole-footer.png'
                ),
                'filters' => [
                    'year' => $this->request->getGet('year'),
                    'month' => $this->request->getGet('month'),
                    'service' => $this->request->getGet('service'),
                    'status' => $this->request->getGet('status'),
                    'search' => $this->request->getGet('q'),
                ]
            ]);

            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');

            ini_set('memory_limit', '512M');
            set_time_limit(120);

            $dompdf->render();
            $pdf = $dompdf->output();

            if (!str_starts_with($pdf, '%PDF-') || !str_contains(substr($pdf, -1024), '%%EOF')) {
                throw new \RuntimeException('Dompdf produced an incomplete PDF.');
            }

            return $this->response
                ->download('HR_Request_Report.pdf', $pdf)
                ->setContentType('application/pdf');
        } catch (Throwable $exception) {
            log_message('error', 'PDF export error: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->response
                ->setStatusCode(500)
                ->setContentType('text/plain')
                ->setBody('PDF export error: ' . $exception->getMessage());
        }
    }
    private function imageDataUri(string $path): ?string
    {
        if (!is_file($path) || !is_readable($path)) {
            log_message('error', 'PDF image not found: {path}', ['path' => $path]);
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    public function printHrRequestReport()
    {
        $builder = $this->getFilteredRequests();

        $requests = $builder
            ->select([
                'reference_no',
                'fullname',
                'service_availed',
                'status',
                'date_requested',
                'date_processing',
                'date_released',
                'date_completed',
                'date_rejected',
            ])
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Service Requests Report',
            'requests' => $requests,
            'filters' => [
                'year' => $this->request->getGet('year'),
                'month' => $this->request->getGet('month'),
                'service' => $this->request->getGet('service'),
                'status' => $this->request->getGet('status'),
                'search' => $this->request->getGet('q'),
            ],
        ];

        return view('exports/request_report_print', $data);
    }
    public function exportReportsPdf()
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(120);

            $requests = $this->getFilteredRequests()
                ->select([
                    'reference_no',
                    'fullname',
                    'service_availed',
                    'date_requested',
                    'status'
                ])
                ->orderBy('id', 'DESC')
                ->findAll();

            $html = view('exports/reports_pdf', [
                'title' => 'Service Requests Report',
                'requests' => $requests,
                'headerImage' => $this->imageDataUri(FCPATH . 'assets/img/dole-header.png'),
                'footerImage' => $this->imageDataUri(FCPATH . 'assets/img/dole-footer.png'),
            ]);

            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->setIsRemoteEnabled(false);
            $options->setIsHtml5ParserEnabled(false);
            $options->set('isFontSubsettingEnabled', true);
            $options->set('chroot', FCPATH);
            $options->set('isPhpEnabled', false);

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            $pdf = $dompdf->output();

            if (!str_starts_with($pdf, '%PDF-') || !str_contains(substr($pdf, -1024), '%%EOF')) {
                throw new \RuntimeException('Dompdf produced an incomplete PDF.');
            }

            $filename = 'Service_Requests_Report_' . date('Y-m-d_His') . '.pdf';

            return $this->response
                ->download($filename, $pdf)
                ->setContentType('application/pdf');
        } catch (Throwable $e) {
            log_message('error', 'PDF export error: {message}', [
                'message' => $e->getMessage(),
            ]);

            return $this->response
                ->setStatusCode(500)
                ->setContentType('text/plain')
                ->setBody('An error occurred while generating the report. Please try again or contact support.');
        }
    }

    public function exportReportsCsv()
    {
        try {

            $requests = $this->getFilteredRequests()
                ->select([
                    'reference_no',
                    'fullname',
                    'service_availed',
                    'date_requested',
                    'status'
                ])
                ->orderBy('id', 'DESC')
                ->findAll();

            $filename = 'Service_Requests_Report_' . date('Y-m-d_His') . '.csv';

            $handle = fopen('php://temp', 'w+');

            // UTF-8 BOM so Excel doesn't mangle special characters
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Reference No.',
                'Requestor',
                'Service Availed',
                'Date Requested',
                'Status'
            ]);

            foreach ($requests as $row) {
                fputcsv($handle, [
                    $row['reference_no'],
                    $row['fullname'],
                    $row['service_availed'],
                    $row['date_requested'],
                    $row['status'],
                ]);
            }

            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);

            return $this->response
                ->download($filename, $csvContent)
                ->setContentType('text/csv');
        } catch (Throwable $e) {

            return $this->response
                ->setStatusCode(500)
                ->setBody($e->getMessage());
        }
    }

    private function getFilteredRequests()
    {
        $builder = $this->requestModel;

        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $service = $this->request->getGet('service');
        $status = $this->request->getGet('status');
        $search = trim($this->request->getGet('q'));

        if (!empty($year)) {
            $builder->where('YEAR(date_requested)', $year);
        }

        if (!empty($month)) {
            $builder->where('MONTH(date_requested)', $month);
        }

        if (!empty($service)) {
            $builder->where('service_availed', $service);
        }

        // Status filter
        if (!empty($status) && $status !== 'All') {
            $builder->where('status', $status);
        }

        // Search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('reference_no', $search)
                ->orLike('fullname', $search)
                ->orLike('service_availed', $search)
                ->groupEnd();
        }

        return $builder;
    }


    public function exportSatisfactionExcel($referenceNo = null)
    {
        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');


        $builder = \Config\Database::connect()->table('client_satisfaction');

        if (!empty($referenceNo)) {
            $builder->where('reference_no', $referenceNo);
        } else {
            if (!empty($year))
                $builder->where('YEAR(date)', $year);
            if (!empty($month))
                $builder->where('MONTH(date)', $month);
        }


        $responses = $builder->orderBy('id', 'ASC')->get()->getResultArray();

        $periodLabel = 'All Records';
        if (!empty($year) && !empty($month)) {
            $periodLabel = date('F', mktime(0, 0, 0, (int) $month, 1)) . ' ' . $year;
        } elseif (!empty($year)) {
            $periodLabel = 'Year ' . $year;
        }

        // 6 CSM dimensions, in the same order as rating_1..rating_6
        $categories = [
            ['group' => 'Responsiveness', 'question' => 'Delivered on a timely manner'],
            ['group' => 'Reliability', 'question' => 'Responded properly to your queries/needs'],
            ['group' => 'Access and Facilities', 'question' => 'Easy access to service/facility'],
            ['group' => 'Integrity', 'question' => 'Feel safe and secure in doing transaction'],
            ['group' => 'Assurance', 'question' => 'Action Officer knowledgeable on DOLE programs/services'],
            ['group' => 'Outcome', 'question' => 'How satisfied are you with the service you received?'],
        ];

        $ratingMap = [
            'Strongly agree' => 5,
            'Agree' => 4,
            'Neither' => 3,
            'Disagree' => 2,
            'Strongly disagree' => 1,
            // anything else (e.g. 'N/A', '') => no response (null)
        ];

        // ---- Build one row per client + running totals, same logic as the xlsx ----
        $rows = [];
        $totals = [];      // totals[$catIndex][5|4|3|2|1] = count
        $noResponse = [];  // noResponse[$catIndex] = count
        foreach (array_keys($categories) as $ci) {
            $totals[$ci] = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
            $noResponse[$ci] = 0;
        }

        foreach ($responses as $i => $r) {
            $ratings = [];
            foreach (array_keys($categories) as $ci) {
                $field = 'rating_' . ($ci + 1);
                $label = trim((string) ($r[$field] ?? ''));
                $score = $ratingMap[$label] ?? null;
                $ratings[$ci] = $score;

                if ($score !== null) {
                    $totals[$ci][$score]++;
                } else {
                    $noResponse[$ci]++;
                }
            }

            $rows[] = [
                'no' => $i + 1,
                'name' => $r['name'] ?? '',
                'gender' => $r['sex'] ?? '',
                'contact' => $r['contact_number'] ?? '',
                'email' => $r['email_address'] ?? '',
                'date' => !empty($r['date']) ? date('M j, Y', strtotime($r['date'])) : '',
                'ratings' => $ratings,
                'complaint' => $r['complaint'] ?? '',
                'suggestion' => $r['suggestions'] ?? '',
                'other_service' => $r['other_service'] ?? 'N/A',
            ];
        }


        $totalClients = count($responses);
        $servicesList = implode(', ', array_values(array_unique(array_column($rows, 'other_service'))));

        return view('exports/satisfaction_excel', [
            'title' => 'Tabulation of Survey Responses',
            'periodLabel' => $periodLabel,
            'categories' => $categories,
            'rows' => $rows,
            'totals' => $totals,
            'noResponse' => $noResponse,
            'totalClients' => $totalClients,
            'servicesList' => $servicesList,
        ]);

    }
}
