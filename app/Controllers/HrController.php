<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\HrModel;
use App\Models\TrainingsModel;
use App\Models\TrainingParticipantsModel;
use App\Models\EmployeeModel;
use App\Models\SuperiorModel;

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

            // Prefill: get the participant if ?p= is in the link
            $pid = $this->request->getGet('p');
            if ($pid !== null && ctype_digit((string) $pid)) {
                $participant = $this->participantsModel
                    ->where('id', (int) $pid)
                    ->where('training_code', $trainingCode)
                    ->first();
            }

            if ($participant && !empty($participant['email'])) {
                $email = strtolower(trim($participant['email']));

                // Already submitted? Show the dedicated page instead of the form
                $done = (new \App\Models\TiaResponsesModel())
                    ->where('training_code', $trainingCode)
                    ->where('email', $email)
                    ->first();

                if ($done) {
                    return view('tia/already_submitted', ['response' => $done]);
                }

                // Office + Immediate Superior from the employees table (matched by email)
                $emp = (new EmployeeModel())
                    ->select('employees.office, s.name AS immediate_superior')
                    ->join('superiors s', 's.id = employees.superior_id', 'left')
                    ->where('LOWER(employees.email)', $email)
                    ->first();

                $participant['office'] = EmployeeModel::normalizeOffice($emp['office'] ?? '')
                    ?? EmployeeModel::normalizeOffice($training['office'] ?? '');
                $participant['immediate_superior'] = $emp['immediate_superior']
                    ?? ($training['immediate_superior'] ?? '');
            }
        }

        if ($training && $this->isFormClosed($training)) {
            return view('tia/form_closed', ['training' => $training]);
        }

        return view('tia/ad_form', [
            'training' => $training,
            'participant' => $participant,
            'competencies' => $training ? (json_decode($training['competencies'] ?? '[]', true) ?: []) : [],
            'offices' => EmployeeModel::OFFICES,
            'superiors' => (new SuperiorModel())->orderBy('name', 'ASC')->findAll(),
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

        if ($this->isFormClosed($training)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'The deadline for this assessment has passed. Please contact HR.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(403);
        }

        if ($exists) {
            return $this->response->setJSON([
                'success' => false,
                'already_submitted' => true,
                'message' => 'You have already submitted an assessment for this training.',
                'reference_no' => $exists['reference_no'],
                'csrf' => csrf_hash(),
            ])->setStatusCode(409);
        }

        if ($this->isFormClosed($training)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'The deadline for this assessment has passed. Please contact HR.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(403);
        }

        // --- Competencies defined by HR for this training (labels come from the DB, not the form) ---
        $comps = json_decode($training['competencies'] ?? '[]', true) ?: [];

        foreach ($comps as $k => $label) {
            $v = (string) $this->request->getPost('comp_' . ($k + 1));
            if (!in_array($v, ['1', '2', '3', '4'], true)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please complete all required fields.',
                    'errors' => ['comp_' . ($k + 1) => 'Please rate: ' . $label],
                    'csrf' => csrf_hash(),
                ])->setStatusCode(422);
            }
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
                    $data["{$prefix}_{$i}_text"] = ($prefix === 'comp' && $comps)
                        ? ($comps[$i - 1] ?? '')
                        : trim((string) $this->request->getPost("{$prefix}_{$i}_text"));
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
            'competencies' => $this->readCompetencies(),
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

        $responses = (new \App\Models\TiaResponsesModel())
            ->select('email, reference_no, created_at')
            ->where('training_code', $trainingCode)
            ->findAll();

        $submitted = [];
        foreach ($responses as $r) {
            $submitted[strtolower(trim($r['email']))] = $r;
        }

        foreach ($participants as &$p) {
            $key = strtolower(trim((string) ($p['email'] ?? '')));
            $p['tia_submitted'] = isset($submitted[$key]);
            $p['tia_reference_no'] = $submitted[$key]['reference_no'] ?? null;
            $p['tia_submitted_at'] = $submitted[$key]['created_at'] ?? null;
        }
        unset($p);

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

        // First time participants are saved for this training -> email the superior (once)
        $notice = null;
        if (
            $inserted > 0
            && empty($training['superior_notified_at'])
            && ($training['status'] ?? 'active') !== 'canceled'
        ) {
            $notice = $this->sendSuperiorNotice($training);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Successfully added {$inserted} participant(s)",
            'count' => $inserted,
            'failed' => $errors,
            'superior_notice' => $notice['message'] ?? null,
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
            'competencies' => $this->readCompetencies(),
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




    private const FORM_DEADLINE_DAYS = 5;

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

        // Deadline: FORM_DEADLINE_DAYS days from now, until 11:59 PM of that day
        $now = date('Y-m-d H:i:s');
        $deadlineAt = date('Y-m-d 23:59:59', strtotime('+' . self::FORM_DEADLINE_DAYS . ' days'));
        $deadlineTxt = date('F j, Y', strtotime($deadlineAt));

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
                'deadline' => $deadlineTxt,
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

        // Record the send: count, last sent, and the new deadline (only if at least one email went out)
        if ($sent > 0) {
            $this->trainingsModel->update($training['id'], [
                'form_sent_at' => $now,
                'form_deadline_at' => $deadlineAt,
                'form_send_count' => ((int) ($training['form_send_count'] ?? 0)) + 1,
            ]);
        }

        $message = "Form sent to {$sent} participant" . ($sent === 1 ? '' : 's') . '.';
        if ($sent > 0) {
            $message .= " Deadline: {$deadlineTxt}.";
        }
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
            'deadline' => $sent > 0 ? $deadlineTxt : null,
            'csrf' => csrf_hash(),
        ]);
    }

    /**
     * Email the training's immediate superior to rate the participants.
     * Called automatically from addParticipantsBulk().
     * Returns ['success' => bool, 'message' => string].
     */
    private function sendSuperiorNotice(array $training): array
    {
        $superiorName = trim((string) ($training['immediate_superior'] ?? ''));
        $superior = $superiorName !== ''
            ? (new SuperiorModel())->where('LOWER(TRIM(name))', strtolower($superiorName))->first()
            : null;

        if (!$superior || empty($superior['email']) || !filter_var($superior['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Superior was not emailed: no superior with a valid email matches "'
                    . $superiorName . '".',
            ];
        }

        $count = $this->participantsModel
            ->where('training_code', $training['training_code'])
            ->countAllResults();

        $mailer = \Config\Services::email();
        $cfg = config('Email');

        $mailer->clear(true);
        $mailer->setMailType('html');
        if (!empty($cfg->fromEmail)) {
            $mailer->setFrom($cfg->fromEmail, $cfg->fromName ?? '');
        }
        $mailer->setTo($superior['email']);
        $mailer->setSubject('Action needed: Rate participants – ' . $training['training_title']);
        $mailer->setMessage(view('tia/email_superior_notify', [
            'superiorName' => $superior['name'],
            'training_title' => $training['training_title'],
            'training_dates' => $training['training_dates'] ?? '',
            'participantCount' => $count,
            'dashboardUrl' => site_url('tia-superior'),
        ]));

        if (!$mailer->send(false)) {
            log_message('error', 'TIA superior notify failed for ' . $superior['email'] . ': '
                . $mailer->printDebugger(['headers']));

            return ['success' => false, 'message' => 'Could not email the superior.'];
        }

        $now = date('Y-m-d H:i:s');
        $newCount = ((int) ($training['superior_notify_count'] ?? 0)) + 1;

        $this->trainingsModel->update($training['id'], [
            'superior_notified_at' => $now,
            'superior_notify_count' => $newCount,
        ]);

        return [
            'success' => true,
            'message' => 'Superior notified: ' . $superior['name'] . ' (' . $superior['email'] . ').',
            'notify_count' => $newCount,
            'notified_at' => date('M d, Y h:i A', strtotime($now)),
        ];
    }

    /**
     * Manually (re)send the "rate participants" email to the superior.
     * POST /tia/notify-superior
     */
    public function notifySuperior()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(403);
        }

        $trainingCode = trim((string) $this->request->getPost('training_code'));
        $training = $trainingCode !== ''
            ? $this->trainingsModel->where('training_code', $trainingCode)->first()
            : null;

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(404);
        }

        $hasParticipants = $this->participantsModel
            ->where('training_code', $trainingCode)
            ->countAllResults() > 0;

        if (!$hasParticipants) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This training has no participants yet.',
                'csrf' => csrf_hash(),
            ]);
        }

        $result = $this->sendSuperiorNotice($training);
        $result['csrf'] = csrf_hash();

        return $this->response->setJSON($result);
    }


    /**
     * Already-submitted page, found by reference number
     * GET /tia_submitted/(:segment)
     */
    public function submitted($referenceNo = null)
    {
        $row = (new \App\Models\TiaResponsesModel())
            ->where('reference_no', (string) $referenceNo)
            ->first();

        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Assessment not found.');
        }

        return view('tia/already_submitted', ['response' => $row]);
    }
    /** Reads competencies[] from the request -> JSON string (max 4) or null. */
    private function readCompetencies(): ?string
    {
        $list = array_values(array_filter(array_map(
            fn($v) => mb_substr(trim((string) $v), 0, 255),
            (array) $this->request->getPost('competencies')
        ), fn($v) => $v !== ''));

        return $list ? json_encode(array_slice($list, 0, 4), JSON_UNESCAPED_UNICODE) : null;
    }

    // -------------------------------------------------------------------------
    // Trainings CSV: template / export / import
    // -------------------------------------------------------------------------

    private const TRAINING_CSV_HEADER = [
        'TRAINING CODE',
        'TRAINING TITLE',
        'TRAINING DATES',
        'CONDUCTED BY',
        'OFFICE',
        'IMMEDIATE SUPERIOR',
        'COMPETENCY 1',
        'COMPETENCY 2',
        'COMPETENCY 3',
        'COMPETENCY 4',
    ];

    /** GET /tia/trainings-template */
    public function trainingsTemplate()
    {
        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, self::TRAINING_CSV_HEADER);
        fputcsv($out, [
            '',
            'Leadership Training',
            'March 10-12, 2026',
            'HR Division',
            'DOLE MIMAROPA REGIONAL OFFICE',
            'John Doe',
            'Communication',
            'Teamwork',
            'Decision Making',
            '',
        ]);
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="trainings_template.csv"')
            ->setBody($csv);
    }

    /** GET /tia/export-trainings */
    public function exportTrainings()
    {
        $rows = $this->trainingsModel->orderBy('created_at', 'DESC')->findAll();

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads it correctly
        fputcsv($out, array_merge(self::TRAINING_CSV_HEADER, ['STATUS']));

        foreach ($rows as $r) {
            $comps = json_decode($r['competencies'] ?? '[]', true) ?: [];
            fputcsv($out, [
                $r['training_code'],
                $r['training_title'],
                $r['training_dates'],
                $r['conducted_by'] ?? '',
                $r['office'] ?? '',
                $r['immediate_superior'] ?? '',
                $comps[0] ?? '',
                $comps[1] ?? '',
                $comps[2] ?? '',
                $comps[3] ?? '',
                $r['status'] ?? 'active',
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="trainings_' . date('Ymd_His') . '.csv"')
            ->setBody($csv);
    }

    /** POST /tia/import-trainings */
    public function importTrainings()
    {
        $back = redirect()->to('/tia_page');
        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid() || strtolower($file->getClientExtension()) !== 'csv') {
            return $back->with('error', 'Please upload a valid .csv file.');
        }

        $handle = fopen($file->getTempName(), 'r');
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return $back->with('error', 'CSV file is empty.');
        }

        // strip BOM, normalize header names
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        $header = array_map(fn($h) => strtoupper(trim((string) $h)), $header);

        $iCode = array_search('TRAINING CODE', $header);       // optional
        $iTitle = array_search('TRAINING TITLE', $header);     // required
        $iDates = array_search('TRAINING DATES', $header);     // required
        $iBy = array_search('CONDUCTED BY', $header);          // optional
        $iOffice = array_search('OFFICE', $header);            // optional
        $iSup = array_search('IMMEDIATE SUPERIOR', $header);   // optional
        $iComp = [];
        for ($n = 1; $n <= 4; $n++) {
            $iComp[$n] = array_search('COMPETENCY ' . $n, $header);   // optional
        }
        $hasComps = count(array_filter($iComp, fn($i) => $i !== false)) > 0;

        if ($iTitle === false || $iDates === false) {
            fclose($handle);
            return $back->with('error', 'Invalid header. Required columns: TRAINING TITLE, TRAINING DATES.');
        }

        $cell = fn(array $row, $idx, int $max) => $idx === false
            ? ''
            : mb_substr(trim((string) ($row[$idx] ?? '')), 0, $max);

        $inserted = $updated = $skipped = 0;
        $now = date('Y-m-d H:i:s');
        $db = db_connect();
        $db->transStart();

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue; // blank line
            }

            $title = $cell($row, $iTitle, 255);
            $dates = $cell($row, $iDates, 100);
            if ($title === '' || $dates === '') {
                $skipped++;
                continue;
            }

            $data = ['training_title' => $title, 'training_dates' => $dates, 'updated_at' => $now];

            // optional columns: only touched when present in the CSV
            if ($iBy !== false) {
                $data['conducted_by'] = $cell($row, $iBy, 150) ?: null;
            }
            if ($iOffice !== false) {
                $data['office'] = $cell($row, $iOffice, 150) ?: null;
            }
            if ($iSup !== false) {
                $data['immediate_superior'] = $cell($row, $iSup, 150) ?: null;
            }
            if ($hasComps) {
                $comps = [];
                foreach ($iComp as $idx) {
                    $v = $cell($row, $idx, 255);
                    if ($v !== '') {
                        $comps[] = $v;
                    }
                }
                $data['competencies'] = $comps ? json_encode($comps, JSON_UNESCAPED_UNICODE) : null;
            }

            // find existing: by code first, then by title + dates
            $existing = null;
            $code = $cell($row, $iCode, 50);
            if ($code !== '') {
                $existing = $this->trainingsModel->where('training_code', $code)->first();
            }
            if (!$existing) {
                $existing = $this->trainingsModel
                    ->where('LOWER(TRIM(training_title))', strtolower($title))
                    ->where('LOWER(TRIM(training_dates))', strtolower($dates))
                    ->first();
            }

            if ($existing) {
                $this->trainingsModel->update($existing['id'], $data) !== false ? $updated++ : $skipped++;
            } else {
                $data['training_code'] = $code !== ''
                    ? $code
                    : 'TRN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
                $data['created_at'] = $now;
                $this->trainingsModel->insert($data) ? $inserted++ : $skipped++;
            }
        }
        fclose($handle);
        $db->transComplete();

        return $back->with('success', "Import done: {$inserted} added, {$updated} updated, {$skipped} skipped.");
    }

    private function isFormClosed(array $training): bool
    {
        return !empty($training['form_deadline_at'])
            && strtotime($training['form_deadline_at']) < time();
    }





}




