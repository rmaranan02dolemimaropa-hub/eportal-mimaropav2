<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\HrModel;
use App\Models\TrainingsModel;
use App\Models\TrainingParticipantsModel;

class HrController extends BaseController
{
    protected $hrModel;
    protected $trainingsModel;
    protected $participantsModel;

    public function __construct()
    {
        $this->hrModel = new HrModel();
        $this->trainingsModel = new TrainingsModel();
        $this->participantsModel = new TrainingParticipantsModel();
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




    // -------------------------------------------------------------------------
    // Dashboard: Trainings + Participants
    // -------------------------------------------------------------------------

    /**
     * Training list page
     * GET /tia_page
     */
    public function tia_page()
    {
        $data['trainings'] = $this->trainingsModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('dashboard/tia', $data);
    }

    /**
     * Store a new Training
     * POST /tia/store-training
     */
    public function storeTraining()
    {
        $rules = [
            'training_title' => 'required|max_length[255]',
            'training_dates' => 'required|max_length[100]',
            'conducted_by' => 'permit_empty|max_length[150]',
            'office' => 'permit_empty|max_length[150]',
            'immediate_superior' => 'permit_empty|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ])->setStatusCode(422);
        }

        $trainingCode = 'TRN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        $data = [
            'training_code' => $trainingCode,
            'training_title' => $this->request->getPost('training_title'),
            'training_dates' => $this->request->getPost('training_dates'),
            'conducted_by' => $this->request->getPost('conducted_by') ?: null,
            'office' => $this->request->getPost('office') ?: null,
            'immediate_superior' => $this->request->getPost('immediate_superior') ?: null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $id = $this->trainingsModel->insert($data);

            if ($id === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to save training',
                    'errors' => $this->trainingsModel->errors(),
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Training added successfully!',
                'id' => $id,
                'training_code' => $trainingCode,
            ])->setStatusCode(201);
        } catch (\Exception $e) {
            log_message('error', 'Store Training Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null,
            ])->setStatusCode(500);
        }
    }

    /**
     * Add a single participant
     * POST /tia/add-participant
     */
    public function addParticipant()
    {
        $rules = [
            'training_code' => 'required|max_length[50]',
            'participant_name' => 'required|max_length[150]',
            'position' => 'required|max_length[150]',
            'email' => 'required|valid_email|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ])->setStatusCode(422);
        }

        $trainingCode = $this->request->getPost('training_code');

        $training = $this->trainingsModel
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found',
            ])->setStatusCode(404);
        }

        $data = [
            'training_code' => $trainingCode,
            'participant_name' => $this->request->getPost('participant_name'),
            'position' => $this->request->getPost('position'),
            'email' => $this->request->getPost('email'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $id = $this->participantsModel->insert($data);

            if ($id === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to add participant',
                    'errors' => $this->participantsModel->errors(),
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Participant added successfully!',
                'id' => $id,
            ])->setStatusCode(201);
        } catch (\Exception $e) {
            log_message('error', 'Add Participant Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null,
            ])->setStatusCode(500);
        }
    }

    /**
     * Get participants for a training
     * GET /tia/participants/(:any)
     */
    public function getParticipants($trainingCode = null)
    {
        if (!$trainingCode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training code is required',
            ])->setStatusCode(400);
        }

        $participants = $this->participantsModel
            ->where('training_code', $trainingCode)
            ->orderBy('id', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $participants,
        ]);
    }

    /**
     * Export participants as CSV
     * GET /tia/export-participants/(:any)
     */
    public function exportParticipants($trainingCode = null)
    {
        if (!$trainingCode) {
            return redirect()->back()->with('error', 'Training code is required');
        }

        $training = $this->trainingsModel
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return redirect()->back()->with('error', 'Training not found');
        }

        $participants = $this->participantsModel
            ->where('training_code', $trainingCode)
            ->orderBy('id', 'ASC')
            ->findAll();

        $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $training['training_title']);
        $filename = 'Participants_' . $safeTitle . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($this->buildParticipantsCsv($participants));
    }

    /**
     * Build CSV string for participants export
     */
    private function buildParticipantsCsv(array $participants): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Name of Participant', 'Position', 'Email Address']);

        foreach ($participants as $p) {
            fputcsv($output, [
                $p['participant_name'] ?? '',
                $p['position'] ?? '',
                $p['email'] ?? '',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv !== false ? $csv : '';
    }

    /**
     * Bulk add participants
     * POST /tia/add-participants-bulk
     */
    public function addParticipantsBulk()
    {
        $json = $this->request->getJSON(true) ?? [];

        $trainingCode = $json['training_code'] ?? null;
        $participants = $json['participants'] ?? [];

        if (!$trainingCode || empty($participants) || !is_array($participants)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training code and participants are required',
            ])->setStatusCode(400);
        }

        $training = $this->trainingsModel
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found',
            ])->setStatusCode(404);
        }

        $inserted = 0;
        $errors = [];

        foreach ($participants as $p) {
            $name = trim($p['participant_name'] ?? '');
            $position = trim($p['position'] ?? '');
            $email = trim($p['email'] ?? '');

            if ($name === '' || $position === '' || $email === '') {
                $errors[] = $name ?: 'Unknown';
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = $name . ' (invalid email)';
                continue;
            }

            $data = [
                'training_code' => $trainingCode,
                'participant_name' => $name,
                'position' => $position,
                'email' => $email,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->participantsModel->insert($data)) {
                $inserted++;
            } else {
                $errors[] = $name;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Successfully added {$inserted} participant(s)",
            'count' => $inserted,
            'failed' => $errors,
        ]);
    }

    /**
     * Update a participant
     * POST /tia/update-participant/(:num)
     */
    public function updateParticipant($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Participant ID is required',
            ])->setStatusCode(400);
        }

        $participant = $this->participantsModel->find($id);
        if (!$participant) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Participant not found',
            ])->setStatusCode(404);
        }

        $rules = [
            'participant_name' => 'required|max_length[150]',
            'position' => 'required|max_length[150]',
            'email' => 'required|valid_email|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ])->setStatusCode(422);
        }

        $data = [
            'participant_name' => $this->request->getPost('participant_name'),
            'position' => $this->request->getPost('position'),
            'email' => $this->request->getPost('email'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $updated = $this->participantsModel->update($id, $data);

            if ($updated === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update participant',
                    'errors' => $this->participantsModel->errors(),
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Participant updated successfully!',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Update Participant Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null,
            ])->setStatusCode(500);
        }
    }

    /**
     * Update a Training (identified by training_code)
     * POST /tia/update-training/(:segment)
     */
    public function updateTraining($trainingCode = null)
    {
        if (!$trainingCode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training code is required',
            ])->setStatusCode(400);
        }

        $training = $this->trainingsModel
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found',
            ])->setStatusCode(404);
        }

        $rules = [
            'training_title' => 'required|max_length[255]',
            'training_dates' => 'required|max_length[100]',
            'conducted_by' => 'permit_empty|max_length[150]',
            'office' => 'permit_empty|max_length[150]',
            'immediate_superior' => 'permit_empty|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ])->setStatusCode(422);
        }

        $data = [
            'training_title' => $this->request->getPost('training_title'),
            'training_dates' => $this->request->getPost('training_dates'),
            'conducted_by' => $this->request->getPost('conducted_by') ?: null,
            'office' => $this->request->getPost('office') ?: null,
            'immediate_superior' => $this->request->getPost('immediate_superior') ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $updated = $this->trainingsModel->update($training['id'], $data);

            if ($updated === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update training',
                    'errors' => $this->trainingsModel->errors(),
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Training updated successfully!',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Update Training Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null,
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete a Training + all its participants (by training_code)
     * POST /tia/delete-training/(:segment)
     */
    public function deleteTraining($trainingCode = null)
    {
        if (!$trainingCode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training code is required',
            ])->setStatusCode(400);
        }

        $training = $this->trainingsModel
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found',
            ])->setStatusCode(404);
        }

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // 1. delete participants first
            $this->participantsModel
                ->where('training_code', $trainingCode)
                ->delete();

            // 2. then delete the training
            $this->trainingsModel
                ->where('training_code', $trainingCode)
                ->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete training',
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Training and its participants were deleted.',
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Delete Training Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null,
            ])->setStatusCode(500);
        }
    }

    /**
     * Update training status (by training_code)
     * POST /tia/update-status/(:segment)
     */
    public function updateTrainingStatus($trainingCode = null)
    {
        if (!$trainingCode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training code is required',
            ])->setStatusCode(400);
        }

        $training = $this->trainingsModel
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found',
            ])->setStatusCode(404);
        }

        $rules = [
            'status' => 'required|in_list[active,canceled,rescheduled,completed]',
            'reason' => 'permit_empty|max_length[1000]',
            'new_dates' => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ])->setStatusCode(422);
        }

        $status = $this->request->getPost('status');
        $reason = trim((string) $this->request->getPost('reason'));
        $newDates = trim((string) $this->request->getPost('new_dates'));
        $now = date('Y-m-d H:i:s');

        if (in_array($status, ['canceled', 'rescheduled'], true) && $reason === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => ['reason' => 'Reason is required for canceled / rescheduled trainings.'],
            ])->setStatusCode(422);
        }

        $data = [
            'status' => $status,
            'updated_at' => $now,
        ];

        if ($status === 'canceled') {
            $data['cancel_reason'] = $reason;
            $data['canceled_at'] = $now;
        }

        if ($status === 'rescheduled') {
            $data['reschedule_reason'] = $reason;
            $data['rescheduled_at'] = $now;
            if ($newDates !== '') {
                $data['training_dates'] = $newDates;
            }
        }

        try {
            $updated = $this->trainingsModel->update($training['id'], $data);

            if ($updated === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update status',
                    'errors' => $this->trainingsModel->errors(),
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status updated successfully!',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Update Status Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null,
            ])->setStatusCode(500);
        }
    }
    /**
     * Delete a single participant
     * POST /tia/delete-participant/(:num)
     */
    public function deleteParticipant($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Participant ID is required',
            ])->setStatusCode(400);
        }

        $participant = $this->participantsModel->find($id);
        if (!$participant) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Participant not found',
            ])->setStatusCode(404);
        }

        try {
            $deleted = $this->participantsModel->delete($id);

            if ($deleted === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete participant',
                    'errors' => $this->participantsModel->errors(),
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Participant deleted successfully!',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Delete Participant Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null,
            ])->setStatusCode(500);
        }
    }




}




