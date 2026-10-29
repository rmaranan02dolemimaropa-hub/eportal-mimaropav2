<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HrRequestModel;
use App\Models\ClientSatisfactionModel;
use App\Models\FeedbackFormModel;

class FeedbackController extends BaseController
{
    //===================================================
    // ADMIN: FEEDBACK LIST
    //===================================================

    public function index()
    {
        $feedbackModel = new FeedbackFormModel();

        $data['title'] = 'Client Satisfaction Feedback';

        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $service = $this->request->getGet('service');
        $rating = $this->request->getGet('rating');
        $search = $this->request->getGet('search');

        $builder = $feedbackModel->orderBy('created_at', 'DESC');

        // ---- Year filter ----
        if (!empty($year)) {
            $builder->where('YEAR(created_at)', $year);
        }

        // ---- Month filter ----
        if (!empty($month)) {
            $builder->where('MONTH(created_at)', $month);
        }

        // ---- Service Availed filter ----
        if (!empty($service)) {
            $builder->where('other_service', $service);
        }

        // ---- Search filter (name / reference no. / service) ----
        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('reference_no', $search)
                ->orLike('other_service', $search)
                ->groupEnd();
        }

        // ---- Overall Rating filter (derived field) ----
        // "Excellent / Very Satisfied / Needs Review" is computed from the
        // rating columns, so when this filter is active we pull all matching
        // rows, bucket them in PHP, then paginate the filtered array manually.
        if (!empty($rating)) {

            $all = $builder->findAll();

            $filtered = array_values(array_filter($all, function ($row) use ($rating) {
                return $this->resolveOverallRating($row) === $rating;
            }));

            $perPage = 10;
            $page = (int) ($this->request->getGet('page') ?? 1);
            $page = max($page, 1);

            $offset = ($page - 1) * $perPage;
            $data['feedbacks'] = array_slice($filtered, $offset, $perPage);

            // Manually build a pager compatible with the view
            $data['pager'] = service('pager');
            $data['pager']->makeLinks($page, $perPage, count($filtered));

        } else {

            $data['feedbacks'] = $builder->paginate(10);
            $data['pager'] = $feedbackModel->pager;
        }

        return view('dashboard/feedback', $data);
    }

    /**
     * Determine the overall rating bucket for a feedback row,
     * based on the same logic used in the view.
     */
    private function resolveOverallRating(array $row): string
    {
        $scores = [
            $row['rating_1'],
            $row['rating_2'],
            $row['rating_3'],
            $row['rating_6'],
            $row['rating_7'],
            $row['rating_8'],
        ];

        $count = array_count_values($scores);

        if (($count['Strongly agree'] ?? 0) >= 4) {
            return 'Excellent';
        } elseif (($count['Agree'] ?? 0) >= 4) {
            return 'Very Satisfied';
        }

        return 'Needs Review';
    }

    //===================================================
    // PRINT
    //===================================================

    public function printEnglish($id)
    {
        $csmModel = new ClientSatisfactionModel();

        $data['feedback'] = $csmModel->find($id);

        if (!$data['feedback']) {
            return redirect()->back()->with('error', 'Feedback not found.');
        }

        return view('exports/feedback_print_english', $data);
    }

    public function printTagalog($id)
    {
        $csmModel = new ClientSatisfactionModel();

        $data['feedback'] = $csmModel->find($id);

        if (!$data['feedback']) {
            return redirect()->back()->with('error', 'Feedback not found.');
        }

        return view('exports/feedback_print_tagalog', $data);
    }

    public function printHr($reference_no)
    {
        $model = new FeedbackFormModel();
        $feedback = $model->where('reference_no', $reference_no)->first();

        if (!$feedback) {
            return redirect()->back()->with('error', 'No HR feedback found for this reference number');
        }

        // DEBUG: Log the rating values to see what's actually stored
        for ($i = 1; $i <= 8; $i++) {
            $val = $feedback['rating_' . $i] ?? 'NULL';
            log_message('debug', "rating_$i = [$val]");
        }

        return view('exports/feedback_print_hr', ['feedback' => $feedback]);
    }

    //===================================================
    // PUBLIC: CLIENT FEEDBACK FORMS
    //===================================================

    public function form($reference_no)
    {
        $requestModel = new HrRequestModel();

        $requests = $requestModel
            ->where('reference_no', $reference_no)
            ->findAll();

        if (!$requests) {
            return redirect()->back()
                ->with('error', 'Reference number not found');
        }

        $servicesAvailed = array_filter(array_unique(array_column($requests, 'service_availed')));
        $combinedServices = implode(', ', $servicesAvailed);

        $request = $requests[0];

        return view('feedback/feedback_form_hr', [
            'reference_no' => $request['reference_no'],
            'service_availed' => $combinedServices,
            'request' => $request,
        ]);
    }

    public function csmTagalog($reference_no)
    {
        $requestModel = new HrRequestModel();

        $request = $requestModel
            ->where('reference_no', $reference_no)
            ->first();

        if (!$request) {
            return redirect()->back()
                ->with('error', 'Reference number not found');
        }

        return view('feedback/feedback_form_tagalog', [
            'reference_no' => $request['reference_no'],
            'service_availed' => $request['service_availed'],
            'request' => $request,
        ]);
    }

    public function save()
    {
        $model = new FeedbackFormModel();
        $requestModel = new HrRequestModel();

        $rules = [
            'reference_no' => 'required',
            'clientType' => 'required',
            'name' => 'required',
            'date' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $reference_no = $this->request->getPost('reference_no');

        // check kung valid pa rin ang reference number
        $request = $requestModel->where('reference_no', $reference_no)->first();
        if (!$request) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid reference number',
            ]);
        }

        // check kung na-submit na dati (avoid duplicate feedback sa same reference)
        $existing = $model->where('reference_no', $reference_no)->first();
        if ($existing) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Feedback for this reference number was already submitted',
            ]);
        }

        $services = $this->request->getPost('service') ?? [];
        $service_availed = is_array($services) ? implode(', ', $services) : $services;

        $data = [
            'reference_no' => $reference_no,
            'client_type' => $this->request->getPost('clientType'),
            'business_name' => $this->request->getPost('businessName'),
            'government_agency' => $this->request->getPost('govAgency'),
            'name' => $this->request->getPost('name'),
            'contact_number' => $this->request->getPost('contact'),
            'email_address' => $this->request->getPost('email'),
            'office_visited' => $this->request->getPost('office'),
            'date' => $this->request->getPost('date'),
            'sex' => $this->request->getPost('sex'),
            'service_availed' => $service_availed,
            'other_service' => $this->request->getPost('otherService'),
            'rating_1' => $this->request->getPost('q0'),
            'rating_2' => $this->request->getPost('q1'),
            'rating_3' => $this->request->getPost('q2'),
            'rating_4' => 'N/A',
            'rating_5' => 'N/A',
            'rating_6' => $this->request->getPost('q5'),
            'rating_7' => $this->request->getPost('q6'),
            'rating_8' => $this->request->getPost('q7'),
            'complaint' => $this->request->getPost('complaint'),
            'suggestions' => $this->request->getPost('comment'),
        ];

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'reference_no' => $reference_no,
        ]);
    }

    public function submitCSM()
    {
        $csmModel = new ClientSatisfactionModel();

        $data = [
            'reference_no' => $this->request->getPost('reference_no'),
            'office_name' => $this->request->getPost('office_name'),
            'transaction_no' => $this->request->getPost('transaction_no'),
            'service_availed' => $this->request->getPost('service_availed'),
            'client_type' => $this->request->getPost('client_type'),
            'date' => $this->request->getPost('date'),
            'sex' => $this->request->getPost('sex'),
            'age' => $this->request->getPost('age'),
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'cc1' => $this->request->getPost('cc1'),
            'cc2' => $this->request->getPost('cc2'),
            'cc3' => $this->request->getPost('cc3'),
            'sqd0' => $this->request->getPost('sqd0'),
            'sqd1' => $this->request->getPost('sqd1'),
            'sqd2' => $this->request->getPost('sqd2'),
            'sqd3' => $this->request->getPost('sqd3'),
            'sqd4' => $this->request->getPost('sqd4'),
            'sqd5' => $this->request->getPost('sqd5'),
            'sqd6' => $this->request->getPost('sqd6'),
            'sqd7' => $this->request->getPost('sqd7'),
            'sqd8' => $this->request->getPost('sqd8'),
            'suggestions' => $this->request->getPost('suggestions'),
            'email' => $this->request->getPost('email'),
        ];

        $result = $csmModel->insert($data);

        if (!$result) {
            log_message('error', 'CSM insert failed: ' . json_encode($csmModel->errors()));

            return redirect()->back()->withInput()->with(
                'error',
                'Sorry, we could not save your response. Please try again.'
            );
        }

        return redirect()->back()->with(
            'success',
            'Thank you for answering our Feedback Survey. Your response has been recorded successfully.'
        );
    }
}