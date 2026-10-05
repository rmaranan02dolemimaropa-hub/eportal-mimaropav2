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
    public function create($trainingCode = null)
    {
        $trainingCode = $trainingCode ?: $this->request->getGet('t');
        $training = null;
        $participant = null;

        if ($trainingCode) {
            $training = $this->trainingsModel
                ->where('training_code', $trainingCode)
                ->first();

            if (!$training) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                    'Training not found: ' . esc($trainingCode)
                );
            }

            // Prefill: kunin ang participant kung may ?p= sa link
            $pid = $this->request->getGet('p');
            if ($pid !== null && ctype_digit((string) $pid)) {
                $participant = $this->participantsModel
                    ->where('id', (int) $pid)
                    ->where('training_code', $trainingCode)   // dapat pareho ang training
                    ->first();
            }
        }

        return view('tia/ad_form', [
            'training' => $training,
            'participant' => $participant,
        ]);
    }
    /**
     * Store the submitted form
     * URL: POST /tia  or  /hr/store
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(403);
        }

        // --- Validation ---
        $required = 'required|in_list[1,2,3,4]';
        $optional = 'permit_empty|in_list[1,2,3,4]';

        $rules = [
            'training_code' => 'required|max_length[50]',
            'participant_name' => 'required|max_length[150]',
            'email' => 'required|valid_email|max_length[150]',
            'position' => 'required|max_length[150]',
            'office' => 'required|max_length[150]',
            'immediate_superior' => 'required|max_length[150]',
            'presented_ideas' => 'required|in_list[Yes,No]',
        ];

        foreach (['att', 'perf', 'app'] as $prefix) {
            for ($i = 1; $i <= 5; $i++) {
                $rules["{$prefix}_{$i}"] = $required;
            }
        }
        foreach (['comp', 'obj'] as $prefix) {
            for ($i = 1; $i <= 4; $i++) {
                $rules["{$prefix}_{$i}"] = $optional;
                $rules["{$prefix}_{$i}_text"] = 'permit_empty|max_length[255]';
            }
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please complete all required fields.',
                'errors' => $this->validator->getErrors(),
                'csrf' => csrf_hash(),
            ])->setStatusCode(422);
        }

        // --- Training: kunin sa DB, huwag magtiwala sa form ---
        $trainingCode = trim((string) $this->request->getPost('training_code'));
        $training = $this->trainingsModel
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(404);
        }

        $tiaModel = new \App\Models\TiaResponsesModel();
        $email = strtolower(trim((string) $this->request->getPost('email')));

        // --- Iwas dobleng sagot ---
        $exists = $tiaModel
            ->where('training_code', $trainingCode)
            ->where('email', $email)
            ->first();

        if ($exists) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You have already submitted an assessment for this training. Reference No.: ' . $exists['reference_no'],
                'csrf' => csrf_hash(),
            ])->setStatusCode(409);
        }

        // --- Barriers (checkbox array) ---
        $allowedBarriers = [
            'no_opportunity',
            'not_enough_time',
            'work_environment',
            'superior_no_support',
            'others',
            'none'
        ];
        $barriers = array_values(array_intersect(
            (array) $this->request->getPost('barrier'),
            $allowedBarriers
        ));

        // --- Reference no. ---
        $referenceNo = 'TIA-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $data = [
            'reference_no' => $referenceNo,
            'training_code' => $trainingCode,
            'training_title' => $training['training_title'],
            'training_dates' => $training['training_dates'] ?? null,
            'participant_name' => trim($this->request->getPost('participant_name')),
            'email' => $email,
            'position' => trim($this->request->getPost('position')),
            'office' => trim($this->request->getPost('office')),
            'immediate_superior' => trim($this->request->getPost('immediate_superior')),

            'other_behavior_changes' => trim((string) $this->request->getPost('other_behavior_changes')),
            'measurable_improvements' => trim((string) $this->request->getPost('measurable_improvements')),

            'barriers' => json_encode($barriers),
            'barrier_others_text' => in_array('others', $barriers, true)
                ? trim((string) $this->request->getPost('barrier_others_text'))
                : null,

            'presented_ideas' => $this->request->getPost('presented_ideas'),
            'ideas_yes' => trim((string) $this->request->getPost('ideas_yes')),
            'ideas_no' => trim((string) $this->request->getPost('ideas_no')),
        ];

        // Ratings
        foreach (['att' => 5, 'perf' => 5, 'app' => 5, 'comp' => 4, 'obj' => 4] as $prefix => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $val = $this->request->getPost("{$prefix}_{$i}");
                $data["{$prefix}_{$i}"] = ($val === null || $val === '') ? null : (int) $val;

                if (in_array($prefix, ['comp', 'obj'], true)) {
                    $data["{$prefix}_{$i}_text"] = trim((string) $this->request->getPost("{$prefix}_{$i}_text"));
                }
            }
        }

        try {
            $tiaModel->insert($data);
        } catch (\Throwable $e) {
            log_message('error', 'TIA submit failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unable to save your assessment. Please try again.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Assessment submitted.',
            'reference_no' => $referenceNo,
            'csrf' => csrf_hash(),
        ]);
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




    public function sendFormEmails()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(403);
        }

        $trainingCode = trim((string) $this->request->getPost('training_code'));
        if ($trainingCode === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training code is required',
                'csrf' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $training = $this->trainingsModel
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found',
                'csrf' => csrf_hash(),
            ])->setStatusCode(404);
        }

        $participants = $this->participantsModel
            ->where('training_code', $trainingCode)
            ->orderBy('id', 'ASC')
            ->findAll();

        if (empty($participants)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This training has no participants yet.',
                'csrf' => csrf_hash(),
            ]);
        }

        @set_time_limit(0);
        ignore_user_abort(true);

        $mailer = \Config\Services::email();
        $cfg = config('Email');

        $sent = 0;
        $failed = [];
        $seen = [];
        $failReason = '';

        foreach ($participants as $p) {
            $to = strtolower(trim((string) ($p['email'] ?? '')));

            if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $failed[] = ($p['participant_name'] ?? 'Unknown') . ' (invalid email)';
                continue;
            }
            if (isset($seen[$to])) {          // same email listed twice -> send once
                continue;
            }
            $seen[$to] = true;

            // Per-participant link: the form opens with this person's details prefilled.
            // -> http://yourdomain/tia_ad?t=TRN-20261005-AB12C&p=12
            $formUrl = site_url('tia_ad')
                . '?t=' . rawurlencode($trainingCode)
                . '&p=' . (int) $p['id'];

            $mailer->clear(true);
            $mailer->setMailType('html');
            if (!empty($cfg->fromEmail)) {
                $mailer->setFrom($cfg->fromEmail, $cfg->fromName ?? '');
            }
            $mailer->setTo($to);
            $mailer->setSubject('Training Impact Assessment: ' . $training['training_title']);
            $mailer->setMessage(view('tia/email_tia_link', [
                'fullname' => $p['participant_name'] ?? '',
                'training_title' => $training['training_title'],
                'training_dates' => $training['training_dates'] ?? '',
                'formUrl' => $formUrl,
            ]));

            if ($mailer->send(false)) {
                $sent++;
            } else {
                $failed[] = ($p['participant_name'] ?? $to) . ' (' . $to . ')';
                $debug = $mailer->printDebugger(['headers']);
                log_message('error', 'TIA form email failed for ' . $to . ': ' . $debug);
                if ($failReason === '') {
                    $failReason = trim(preg_replace('/\s+/', ' ', strip_tags($debug)));
                }
            }
        }

        $message = "Form sent to {$sent} participant" . ($sent === 1 ? '' : 's') . '.';
        if ($failed) {
            $message .= ' Failed: ' . implode(', ', $failed) . '.';
            if (ENVIRONMENT === 'development' && $failReason !== '') {
                $message .= ' Reason: ' . mb_substr($failReason, 0, 400);   // shows the SMTP error on screen (dev only)
            }
        }

        return $this->response->setJSON([
            'success' => $sent > 0,
            'message' => $message,
            'sent' => $sent,
            'failed' => $failed,
            'csrf' => csrf_hash(),
        ]);
    }




}




