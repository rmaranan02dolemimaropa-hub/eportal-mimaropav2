<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HrRequestModel;

class ReportController extends BaseController
{
    //===================================================
    // HR REQUEST REPORTS
    //===================================================

    public function index()
    {
        $model = new HrRequestModel();

        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $service = $this->request->getGet('service');
        $search = $this->request->getGet('q');

        if (!empty($search)) {
            $model->groupStart()
                ->like('fullname', $search)
                ->orLike('reference_no', $search)
                ->groupEnd();
        }

        if (!empty($year)) {
            $model->where('YEAR(date_requested)', $year);
        }

        if (!empty($month)) {
            $model->where('MONTH(date_requested)', $month);
        }

        if (!empty($service)) {
            $model->where('service_availed', $service);
        }

        $data = [
            'requests' => $model->orderBy('id', 'DESC')->paginate(10),
            'pager' => $model->pager,
            'year' => $year,
            'month' => $month,
            'service' => $service,
            'search' => $search,
        ];

        return view('dashboard/reports', $data);
    }
}