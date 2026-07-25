<?php

namespace App\Controllers;

use App\Models\HrRequestModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportController extends BaseController
{
    protected $requestModel;

    public function __construct()
    {
        $this->requestModel = new HrRequestModel();
    }

    public function exportHrRequestReport()
    {
        $requests = $this->requestModel
            ->select('
            reference_no AS reference,
            fullname AS name,
            service_availed AS service,
            status
        ')
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'title' => 'HR Request Report',
            'requests' => $requests,
        ];

        $options = new Options();
        $options->set('chroot', FCPATH);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('exports/request_report', $data), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader(
                'Content-Disposition',
                'attachment; filename="HR_Request_Report.pdf"'
            )
            ->setBody($dompdf->output());

        $query = $this->requestModel;

        if ($year = $this->request->getGet('year')) {
            $query->like('date_requested', $year, 'after');
        }

        if ($status = $this->request->getGet('status')) {
            $query->like('status', $status);
        }

        if ($service = $this->request->getGet('service_availed')) {
            $query->like('service_availed', $service);
        }

        if ($unit = $this->request->getGet('office_division')) {
            $query->like('office_division', $unit);
        }

        if ($dateRequested = $this->request->getGet('date_requested')) {
            $query->where('date_requested', $dateRequested);
        }

        $requests = $query
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}