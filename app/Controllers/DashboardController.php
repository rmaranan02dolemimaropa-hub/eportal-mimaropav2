<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HrRequestModel;

class DashboardController extends BaseController
{
    //===================================================
    // DASHBOARD
    //===================================================

    public function index()
    {
        $response = service('response');
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');

        $requestModel = new HrRequestModel();

        $data = [
            'new_application' => $requestModel->where('status', 'New Application')->countAllResults(),
            'processing' => $requestModel->where('status', 'Processing')->countAllResults(),
            'completed' => $requestModel->where('status', 'Completed')->countAllResults(),
            'for_releasing' => $requestModel->where('status', 'For Releasing')->countAllResults(),
            'rejected' => $requestModel->where('status', 'Rejected')->countAllResults(),
        ];

        $data['requests'] = $requestModel
            ->orderBy("
CASE
    WHEN status = 'New Application' THEN 1
    WHEN status = 'Processing' THEN 2
    WHEN status = 'For Releasing' THEN 3
    WHEN status = 'Completed' THEN 4
    WHEN status = 'Rejected' THEN 5
    WHEN status = 'Canceled' THEN 6
    ELSE 7
END
", '', false)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $data['pager'] = $requestModel->pager;

        $data['new_today'] = $requestModel
            ->where('status', 'New Application')
            ->where('DATE(date_requested)', date('Y-m-d'))
            ->countAllResults();

        $data['processing_today'] = $requestModel
            ->where('status', 'Processing')
            ->where('DATE(date_processing)', date('Y-m-d'))
            ->countAllResults();

        $data['releasing_today'] = $requestModel
            ->where('status', 'For Releasing')
            ->where('DATE(date_released)', date('Y-m-d'))
            ->countAllResults();

        $data['completed_month'] = $requestModel
            ->where('status', 'Completed')
            ->where('YEAR(date_completed)', date('Y'))
            ->where('MONTH(date_completed)', date('m'))
            ->countAllResults();

        $data['rejected_month'] = $requestModel
            ->where('status', 'Rejected')
            ->where('YEAR(date_rejected)', date('Y'))
            ->where('MONTH(date_rejected)', date('m'))
            ->countAllResults();

        $data['canceled_month'] = $requestModel
            ->where('status', 'Canceled')
            ->where('YEAR(date_canceled)', date('Y'))
            ->where('MONTH(date_canceled)', date('m'))
            ->countAllResults();

        return view('dashboard/dashboard', $data);
    }
}