<?php

namespace App\Controllers;

use App\Models\TrainingsModel;
use App\Models\TrainingParticipantsModel;
use App\Models\TiaSuperiorResponsesModel;

class TiaSuperiorController extends BaseController
{
    protected $trainingsModel;
    protected $participantsModel;

    protected $responsesModel;

    public function __construct()
    {
        $this->trainingsModel = new TrainingsModel();
        $this->participantsModel = new TrainingParticipantsModel();
        $this->responsesModel = new TiaSuperiorResponsesModel();
    }

    /** Every scalar value stored in the session, flattened. */
    private function sessionValues(): array
    {
        $out = [];
        $walk = function ($v) use (&$walk, &$out) {
            if (is_array($v)) {
                foreach ($v as $x)
                    $walk($x);
            } elseif (is_object($v)) {
                $walk((array) $v);
            } elseif (is_scalar($v)) {
                $out[] = trim((string) $v);
            }
        };
        $walk(session()->get());

        return array_values(array_unique(array_filter($out, fn($s) => $s !== '')));
    }

    /** Resolve the logged-in superior without relying on specific session keys. */
    private function currentSuperior(): array
    {
        $db = \Config\Database::connect();
        $session = session();
        $vals = array_map('strtolower', $this->sessionValues());

        $sup = null;

        // 1. any session value that is a superior's email
        if ($vals) {
            $sup = $db->table('superiors')
                ->whereIn('LOWER(email)', $vals)
                ->get()->getRowArray();
        }

        // 2. any session value that is a superior's name
        if (!$sup && $vals) {
            $sup = $db->table('superiors')
                ->whereIn('LOWER(name)', $vals)
                ->get()->getRowArray();
        }

        // 3. via the users table (id / email / username / fullname) -> superiors.email
        if (!$sup) {
            $uid = $session->get('user_id') ?? $session->get('id')
                ?? $session->get('userId') ?? $session->get('uid');

            if ($uid || $vals) {
                $q = $db->table('users')->select('email')->where('role', 'SUPERIOR');
                $q->groupStart();
                if ($uid) {
                    $q->orWhere('id', $uid);
                }
                if ($vals) {
                    $q->orWhereIn('LOWER(email)', $vals)
                        ->orWhereIn('LOWER(username)', $vals)
                        ->orWhereIn('LOWER(fullname)', $vals);
                }
                $q->groupEnd();
                $u = $q->get()->getRowArray();

                if ($u && !empty($u['email'])) {
                    $sup = $db->table('superiors')
                        ->where('LOWER(email)', strtolower($u['email']))
                        ->get()->getRowArray();
                }
            }
        }

        return [
            'id' => $sup['id'] ?? null,
            'name' => $sup['name'] ?? null,
            'position' => $sup['position'] ?? null,
        ];
    }

    /** Trainings that belong to this superior. */
    private function trainingsQuery()
    {
        $sup = $this->currentSuperior();

        // no superior resolved -> match nothing
        if (empty($sup['name'])) {
            return $this->trainingsModel->where('1 = 0', null, false);
        }

        return $this->trainingsModel
            ->where('LOWER(TRIM(immediate_superior))', strtolower(trim($sup['name'])));
    }

    /**
     * Page: list of trainings for the logged-in superior
     * GET /tia-superior
     */
    public function index()
    {
        $data['trainings'] = $this->trainingsQuery()
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('dashboard/tiasuperior', $data);
    }

    /**
     * Participants of a training + whether the superior has rated each one
     * GET /tia-superior/participants/(:segment)
     */
    public function getParticipants($trainingCode = null)
    {
        if (!$trainingCode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training code is required',
            ])->setStatusCode(400);
        }

        // the training must belong to this superior
        $training = $this->trainingsQuery()
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found',
            ])->setStatusCode(404);
        }

        $participants = $this->participantsModel
            ->where('training_code', $trainingCode)
            ->orderBy('id', 'ASC')
            ->findAll();

        // Superior ratings already submitted (Annex E)
        $rows = \Config\Database::connect()
            ->table('tia_superior_responses')
            ->select('participant_id, reference_no, created_at')
            ->where('training_code', $trainingCode)
            ->get()
            ->getResultArray();

        $rated = [];
        foreach ($rows as $r) {
            $rated[(int) $r['participant_id']] = $r;
        }

        foreach ($participants as &$p) {
            $row = $rated[(int) $p['id']] ?? null;
            $p['rated'] = $row !== null;
            $p['rating_ref'] = $row['reference_no'] ?? null;
            $p['rated_at'] = $row['created_at'] ?? null;
        }
        unset($p);

        return $this->response->setJSON([
            'success' => true,
            'data' => $participants,
        ]);
    }

    /**
     * Rating sheet (Annex E) for one participant
     * GET /tia-superior/rate/(:segment)/(:num)
     */
    public function rate($trainingCode = null, $participantId = null)
    {
        // training must belong to this superior
        $training = $this->trainingsQuery()
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
        }

        $participant = $this->participantsModel
            ->where('id', (int) $participantId)
            ->where('training_code', $trainingCode)
            ->first();

        if (!$participant) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Participant not found.');
        }

        // already rated -> back to the list
        $done = $this->responsesModel
            ->where('training_code', $trainingCode)
            ->where('participant_id', (int) $participantId)
            ->first();

        if ($done) {
            return redirect()->to(site_url('tia-superior'))
                ->with('error', 'You already rated this participant (Ref: ' . $done['reference_no'] . ').');
        }

        $sup = $this->currentSuperior();

        return view('tia/ae_form', [
            'training' => $training,
            'participant' => $participant,
            'superior' => ['name' => $sup['name'], 'position' => $sup['position']],
        ]);
    }

    /**
     * Save the rating sheet
     * POST /tia_superior_store
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
            'participant_id' => 'required|is_natural_no_zero',
        ];
        foreach (['att', 'perf', 'app'] as $prefix) {
            for ($i = 1; $i <= 5; $i++) {
                $rules["{$prefix}_{$i}"] = $required;
            }
        }
        for ($i = 1; $i <= 5; $i++) {
            $rules["comp_{$i}"] = $optional;
            $rules["comp_{$i}_text"] = 'permit_empty|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please complete all required fields.',
                'errors' => $this->validator->getErrors(),
                'csrf' => csrf_hash(),
            ])->setStatusCode(422);
        }

        $trainingCode = trim((string) $this->request->getPost('training_code'));
        $participantId = (int) $this->request->getPost('participant_id');

        // --- Ownership: take everything from the DB, not from the form ---
        $training = $this->trainingsQuery()
            ->where('training_code', $trainingCode)
            ->first();

        if (!$training) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Training not found.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(404);
        }

        $participant = $this->participantsModel
            ->where('id', $participantId)
            ->where('training_code', $trainingCode)
            ->first();

        if (!$participant) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Participant not found.',
                'csrf' => csrf_hash(),
            ])->setStatusCode(404);
        }

        // --- No double rating ---
        $exists = $this->responsesModel
            ->where('training_code', $trainingCode)
            ->where('participant_id', $participantId)
            ->first();

        if ($exists) {
            return $this->response->setJSON([
                'success' => false,
                'already_submitted' => true,
                'message' => 'You already rated this participant.',
                'reference_no' => $exists['reference_no'],
                'csrf' => csrf_hash(),
            ])->setStatusCode(409);
        }

        // --- Part III: 2+ Critical Success Factors rated 1-2 need an explanation ---
        $low = 0;
        for ($i = 1; $i <= 5; $i++) {
            if ((int) $this->request->getPost("app_{$i}") <= 2) {
                $low++;
            }
        }
        $deferment = trim((string) $this->request->getPost('deferment_reason'));
        if ($low >= 2 && $deferment === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please complete all required fields.',
                'errors' => ['deferment_reason' => 'Explain why the new knowledge/skills were not applied (Part III).'],
                'csrf' => csrf_hash(),
            ])->setStatusCode(422);
        }

        // --- Reasons for sending staff (checkbox array) ---
        $allowedReasons = [
            'long_term_strategy',
            'competitive_advantage',
            'operational_efficiency',
            'ongoing_projects',
            'employee_development',
            'others',
        ];
        $reasons = array_values(array_intersect(
            (array) $this->request->getPost('reason'),
            $allowedReasons
        ));

        $sup = $this->currentSuperior();
        $referenceNo = 'TIAS-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $data = [
            'reference_no' => $referenceNo,
            'training_code' => $trainingCode,
            'training_title' => $training['training_title'],
            'training_dates' => $training['training_dates'] ?? null,
            'participant_id' => $participantId,
            'participant_name' => $participant['participant_name'],
            'participant_position' => $participant['position'],
            'participant_email' => $participant['email'] ?? null,
            'superior_name' => $sup['name'] ?? $training['immediate_superior'],
            'superior_position' => $sup['position'] ?? trim((string) $this->request->getPost('superior_position')),

            'other_behavior_changes' => trim((string) $this->request->getPost('other_behavior_changes')),
            'measurable_improvements' => trim((string) $this->request->getPost('measurable_improvements')),
            'deferment_reason' => $deferment,
            'reasons' => json_encode($reasons),
            'reason_others_text' => in_array('others', $reasons, true)
                ? trim((string) $this->request->getPost('reason_others_text'))
                : null,
        ];

        foreach (['att' => 5, 'perf' => 5, 'app' => 5, 'comp' => 5] as $prefix => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $val = $this->request->getPost("{$prefix}_{$i}");
                $data["{$prefix}_{$i}"] = ($val === null || $val === '') ? null : (int) $val;

                if ($prefix === 'comp') {
                    $data["comp_{$i}_text"] = trim((string) $this->request->getPost("comp_{$i}_text"));
                }
            }
        }

        try {
            $this->responsesModel->insert($data);
        } catch (\Throwable $e) {
            log_message('error', 'TIA superior rating failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unable to save the assessment. Please try again.',
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

    /** Returns ['q' => 1-4, 'y' => 2026] from a free-text training_dates value, or null. */
    private function quarterOf(?string $dates): ?array
    {
        $dates = trim((string) $dates);
        if ($dates === '') {
            return null;
        }

        $month = null;
        if (preg_match('/\b(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*\b/i', $dates, $m)) {
            $month = (int) date('n', strtotime('1 ' . $m[1] . ' 2000'));
        } elseif (($ts = strtotime($dates)) !== false) {
            $month = (int) date('n', $ts);
        }
        if ($month === null) {
            return null;
        }

        $year = preg_match('/\b(20\d{2})\b/', $dates, $y) ? (int) $y[1] : (int) date('Y');

        return ['q' => (int) ceil($month / 3), 'y' => $year];
    }

    /**
     * Monitoring: every participant of this superior's trainings, rated or pending
     * GET /tia-superior/monitoring?status=&quarter=&year=&s=
     */
    public function monitoring()
    {
        $status = $this->request->getGet('status');
        $status = in_array($status, ['pending', 'rated', 'all'], true) ? $status : 'pending';

        $quarter = (int) $this->request->getGet('quarter');
        $quarter = ($quarter >= 1 && $quarter <= 4) ? $quarter : 0;
        $year = (int) $this->request->getGet('year');
        $search = trim((string) $this->request->getGet('s'));

        $trainings = $this->trainingsQuery()->orderBy('created_at', 'DESC')->findAll();
        $rows = [];

        if ($trainings) {
            $byCode = [];
            foreach ($trainings as $t) {
                $byCode[$t['training_code']] = $t;
            }
            $codes = array_keys($byCode);

            $participants = $this->participantsModel
                ->whereIn('training_code', $codes)
                ->orderBy('id', 'ASC')
                ->findAll();

            $rated = [];
            $res = \Config\Database::connect()
                ->table('tia_superior_responses')
                ->select('participant_id, reference_no, created_at')
                ->whereIn('training_code', $codes)
                ->get()->getResultArray();
            foreach ($res as $r) {
                $rated[(int) $r['participant_id']] = $r;
            }

            foreach ($participants as $p) {
                $t = $byCode[$p['training_code']] ?? null;
                if (!$t) {
                    continue;
                }
                $qy = $this->quarterOf($t['training_dates'] ?? '');
                $r = $rated[(int) $p['id']] ?? null;

                $rows[] = [
                    'participant_id' => (int) $p['id'],
                    'participant_name' => $p['participant_name'] ?? '',
                    'position' => $p['position'] ?? '',
                    'email' => $p['email'] ?? '',
                    'training_code' => $t['training_code'],
                    'training_title' => $t['training_title'],
                    'training_dates' => $t['training_dates'] ?? '',
                    'quarter' => $qy['q'] ?? null,
                    'year' => $qy['y'] ?? null,
                    'rated' => $r !== null,
                    'rating_ref' => $r['reference_no'] ?? null,
                    'rated_at' => $r['created_at'] ?? null,
                ];
            }
        }

        // Years for the dropdown (before filtering)
        $years = array_values(array_unique(array_filter(array_column($rows, 'year'))));
        rsort($years);

        // Quarter / year
        if ($quarter || $year) {
            $rows = array_filter(
                $rows,
                fn($r) =>
                    $r['quarter'] !== null
                    && (!$quarter || $r['quarter'] === $quarter)
                    && (!$year || $r['year'] === $year)
            );
        }

        // Search
        if ($search !== '') {
            $needle = mb_strtolower($search);
            $rows = array_filter($rows, function ($r) use ($needle) {
                $hay = mb_strtolower(implode(' ', [
                    $r['participant_name'],
                    $r['position'],
                    $r['email'],
                    $r['training_title'],
                    $r['training_dates'],
                    $r['rating_ref'] ?? '',
                ]));
                return str_contains($hay, $needle);
            });
        }
        $rows = array_values($rows);

        // Totals follow quarter/year/search, before the status tab
        $ratedCount = count(array_filter($rows, fn($r) => $r['rated']));
        $totals = [
            'total' => count($rows),
            'rated' => $ratedCount,
            'pending' => count($rows) - $ratedCount,
        ];

        $rows = array_values(array_filter(
            $rows,
            fn($r) =>
                $status === 'all' || ($status === 'pending' ? !$r['rated'] : $r['rated'])
        ));

        return view('dashboard/tiasuperiormonitoring', [
            'rows' => $rows,
            'totals' => $totals,
            'status' => $status,
            'quarter' => $quarter,
            'year' => $year,
            'years' => $years,
            'search' => $search,
        ]);
    }
}