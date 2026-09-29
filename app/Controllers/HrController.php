<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\HrModel;

class HrController extends BaseController
{
    protected $hrModel;

    public function __construct()
    {
        $this->hrModel = new HrModel();
    }

    /**
     * Show the Training Impact Assessment form
     * URL: /tia  or  /hr/create
     */
    public function create()
    {
        return view('tia/ad_form');   // resources/views/tia/form.php  (or .html)
    }

    /**
     * Store the submitted form
     * URL: POST /tia  or  /hr/store
     */
    public function store()
    {
        // 1. Log everything we received
        log_message('error', '=== TIA FORM SUBMITTED ===');
        log_message('error', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('error', 'Raw input: ' . $this->request->getBody());

        $request = $this->request;

        // ===== Temporary relaxed validation (for debugging only) =====
        $rules = [
            'training_title' => 'required',
            'participant_name' => 'required',
            'email' => 'required|valid_email',
            'presented_ideas' => 'required|in_list[Yes,No]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
                'received_data' => $this->request->getPost()
            ])->setStatusCode(422);
        }

        // Prepare data
        $barriers = $request->getPost('barrier') ?? [];
        $barriersStr = is_array($barriers) ? implode(',', $barriers) : null;

        // ===== Generate Reference Number → TIA20260929A7K9X2 =====
        $datePart = date('Ymd');                          // e.g. 20260929
        $characters = '0123456789';
        $randomPart = '';

        for ($i = 0; $i < 6; $i++) {
            $randomPart .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $referenceNo = 'TIA-' . $datePart . $randomPart;

        $data = [
            'reference_no' => $referenceNo,   // ← added

            'training_title' => $request->getPost('training_title'),
            'training_dates' => $request->getPost('training_dates'),
            'participant_name' => $request->getPost('participant_name'),
            'email' => $request->getPost('email'),
            'position' => $request->getPost('position'),
            'office' => $request->getPost('office'),
            'immediate_superior' => $request->getPost('immediate_superior'),

            'att_1' => $request->getPost('att_1'),
            'att_2' => $request->getPost('att_2'),
            'att_3' => $request->getPost('att_3'),
            'att_4' => $request->getPost('att_4'),
            'att_5' => $request->getPost('att_5'),

            'perf_1' => $request->getPost('perf_1'),
            'perf_2' => $request->getPost('perf_2'),
            'perf_3' => $request->getPost('perf_3'),
            'perf_4' => $request->getPost('perf_4'),
            'perf_5' => $request->getPost('perf_5'),

            'comp_1_text' => $request->getPost('comp_1_text'),
            'comp_1' => $request->getPost('comp_1'),
            'comp_2_text' => $request->getPost('comp_2_text'),
            'comp_2' => $request->getPost('comp_2'),
            'comp_3_text' => $request->getPost('comp_3_text'),
            'comp_3' => $request->getPost('comp_3'),
            'comp_4_text' => $request->getPost('comp_4_text'),
            'comp_4' => $request->getPost('comp_4'),

            'obj_1_text' => $request->getPost('obj_1_text'),
            'obj_1' => $request->getPost('obj_1'),
            'obj_2_text' => $request->getPost('obj_2_text'),
            'obj_2' => $request->getPost('obj_2'),
            'obj_3_text' => $request->getPost('obj_3_text'),
            'obj_3' => $request->getPost('obj_3'),
            'obj_4_text' => $request->getPost('obj_4_text'),
            'obj_4' => $request->getPost('obj_4'),

            'app_1' => $request->getPost('app_1'),
            'app_2' => $request->getPost('app_2'),
            'app_3' => $request->getPost('app_3'),
            'app_4' => $request->getPost('app_4'),
            'app_5' => $request->getPost('app_5'),

            'other_behavior_changes' => $request->getPost('other_behavior_changes'),
            'measurable_improvements' => $request->getPost('measurable_improvements'),

            'barriers' => $barriersStr,
            'barrier_others_text' => $request->getPost('barrier_others_text'),

            'presented_ideas' => $request->getPost('presented_ideas'),
            'ideas_yes' => $request->getPost('ideas_yes'),
            'ideas_no' => $request->getPost('ideas_no'),

            'submitted_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $insertedId = $this->hrModel->insert($data);

            if ($insertedId === false) {
                // Model validation or database error
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Model insert failed',
                    'model_errors' => $this->hrModel->errors(),
                    'data_tried' => $data
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Training Impact Assessment submitted successfully!',
                'reference_no' => $referenceNo,
                'id' => $insertedId
            ])->setStatusCode(201);

        } catch (\Exception $e) {
            log_message('error', 'TIA Store Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exception occurred',
                'error' => $e->getMessage(),
                'data' => $data
            ])->setStatusCode(500);
        }
    }

    /**
     * List all submissions (for admin)
     */
    public function index()
    {
        $records = $this->hrModel
            ->orderBy('submitted_at', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $records
        ]);
    }

    /**
     * Show single record
     */
    public function show($id = null)
    {
        $record = $this->hrModel->find($id);

        if (!$record) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Record not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $record
        ]);
    }
}
