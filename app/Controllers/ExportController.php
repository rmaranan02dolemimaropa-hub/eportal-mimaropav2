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
            $options->setIsRemoteEnabled(true);
            $options->setIsHtml5ParserEnabled(true);

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            return $this->response
                ->download('Service_Requests_Report.pdf', $dompdf->output())
                ->setContentType('application/pdf');

        } catch (Throwable $e) {

            return $this->response
                ->setStatusCode(500)
                ->setBody($e->getMessage());
        }
    }

    public function exportReportsCsv()
    {
        $requests = $this->getFilteredRequests()
            ->orderBy('id', 'DESC')
            ->findAll();

        $filename = 'Service_Requests_Report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Reference No.',
            'Requestor',
            'Service',
            'Date Requested',
            'Status'
        ]);

        foreach ($requests as $row) {

            fputcsv($output, [
                $row['reference_no'],
                $row['fullname'],
                $row['service_availed'],
                date('F d, Y', strtotime($row['date_requested'])),
                $row['status']
            ]);

        }

        fclose($output);
        exit;
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


}