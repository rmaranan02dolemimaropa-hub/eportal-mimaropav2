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
            ->orderBy('id', 'DESC')
            ->findAll();

        // Temporary check: uncomment this line to see returned data.
        // dd($requests);

        $data = [
            'title'    => 'HR Request Report',
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
    }
}