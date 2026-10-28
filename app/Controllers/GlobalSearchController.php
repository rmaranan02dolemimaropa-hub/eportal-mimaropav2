<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\SuperiorModel;
use App\Models\TrainingsModel;
use App\Models\UserModel;

/**
 * Navbar search: one box across pages, HR requests, TIA trainings, employees,
 * superiors, users and activity logs. Returns JSON only; the browser builds the
 * list with textContent, so nothing here is HTML.
 *
 *   GET /search?q=juan
 *
 * Who sees what:
 *   everyone logged in ..... pages they may open + HR requests
 *   IT / HR administrators . + TIA trainings, employees, superiors
 *   IT administrator only .. + users, activity logs
 *
 * A group that throws (missing column, bad table) is logged and skipped,
 * so one broken group never breaks the whole search.
 */
class GlobalSearchController extends BaseController
{
    private const LIMIT = 5;

    public function index()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $role = strtoupper(trim((string) session()->get('role')));
        if ($role === '') {
            return $this->response->setStatusCode(401)->setJSON(['groups' => []]);
        }

        $q = mb_substr(trim((string) $this->request->getGet('q')), 0, 100);
        if (mb_strlen($q) < 2) {
            return $this->response->setJSON(['groups' => []]);
        }

        $isIT = $role === 'IT ADMINISTRATOR';
        $isAdmin = $isIT || $role === 'HR ADMINISTRATOR';

        $groups = [
            $this->pages($q, $role),
            $this->requests($q),
        ];

        if ($isAdmin) {
            $groups[] = $this->trainings($q);
            $groups[] = $this->employees($q);
            $groups[] = $this->superiors($q);
        }
        if ($isIT) {
            $groups[] = $this->users($q);
            $groups[] = $this->logs($q);
        }

        return $this->response->setJSON(['groups' => array_values(array_filter($groups))]);
    }

    // ------------------------------------------------------------------
    // Pages
    // ------------------------------------------------------------------

    /** Sidebar pages. Add or fix a row here when a route changes. */
    private function pages(string $q, string $role): ?array
    {
        $admin = ['IT ADMINISTRATOR', 'HR ADMINISTRATOR'];
        $it = ['IT ADMINISTRATOR'];

        $pages = [
            // title, url, icon, keywords, roles allowed (null = everyone)
            ['Dashboard', site_url('dashboard'), 'bi-grid', 'home overview statistics', null],
            ['Requests', site_url('request'), 'bi-file-earmark-text', 'hr service requests', null],
            ['Reports', site_url('reports'), 'bi-bar-chart', 'export pdf filter hr requests report', $admin],
            ['Users', site_url('users'), 'bi-people', 'accounts roles mfa deactivate', $it],
            ['Activity Logs', site_url('activity-logs'), 'bi-clock-history', 'audit history sign in logins', $it],
            ['Employees', site_url('employees'), 'bi-person-vcard', 'dole staff permanent non-permanent import csv', $admin],
            ['Superiors', site_url('superiors'), 'bi-person-badge', 'immediate superior heads', $admin],
            ['TIA · Annex D-1 (Self-Rater)', site_url('tia-report'), 'bi-mortarboard', 'training impact assessment participants', $admin],
            ['TIA · Annex E-1 (Superior-Rater)', site_url('tia-report-superior'), 'bi-mortarboard', 'training impact assessment superiors', $admin],
            ['TIA · Annex G (Participants by Office)', site_url('tia-report-annex-g'), 'bi-mortarboard', 'training participants office', $admin],
            ['TIA · Annex H (Public Offerings)', site_url('tia-report-annex-h'), 'bi-mortarboard', 'training impact public', $admin],
            ['TIA · Annex J', site_url('tia-report-annex-j'), 'bi-mortarboard', 'training impact form', $admin],
            ['TIA · Superior Monitoring', site_url('tia-monitoring'), 'bi-clipboard-check', 'pending rated superiors', $admin],
            ['TIA · Participant Monitoring', site_url('tia-report-participant-monitoring'), 'bi-clipboard-check', 'pending rated participants', $admin],
            ['TIA · Bulk Reports', site_url('tia-report-bulk'), 'bi-collection', 'quarter year print export excel', $admin],
        ];

        $items = [];
        foreach ($pages as [$title, $url, $icon, $keywords, $roles]) {
            if ($roles !== null && !in_array($role, $roles, true)) {
                continue;
            }
            if (stripos($title . ' ' . $keywords, $q) !== false) {
                $items[] = ['title' => $title, 'sub' => 'Page', 'url' => $url, 'icon' => $icon];
            }
        }

        return $items ? ['label' => 'Pages', 'items' => array_slice($items, 0, 8)] : null;
    }

    // ------------------------------------------------------------------
    // Data groups
    // ------------------------------------------------------------------

    private function requests(string $q): ?array
    {
        try {
            $db = db_connect();
            $match = static function ($b) use ($q) {
                return $b->groupStart()
                    ->like('reference_no', $q)
                    ->orLike('fullname', $q)
                    ->orLike('service_availed', $q)
                    ->orLike('email_address', $q)
                    ->groupEnd();
            };

            $rows = $match($db->table('hr_requests'))
                ->select('id, reference_no, fullname, service_availed, status')
                ->orderBy('id', 'DESC')
                ->limit(self::LIMIT)
                ->get()->getResultArray();

            if (!$rows) {
                return null;
            }

            $items = [];
            foreach ($rows as $r) {
                $items[] = [
                    'title' => $r['fullname'],
                    'sub' => $r['service_availed'] . ' · ' . $r['reference_no'],
                    'badge' => $r['status'],
                    'icon' => 'bi-file-earmark-text',
                    // search= narrows the table to this request; notif/ref/svc highlight the row
                    'url' => site_url('request') . '?' . http_build_query([
                        'search' => $r['reference_no'],
                        'notif' => 1,
                        'ref' => $r['reference_no'],
                        'svc' => $r['service_availed'],
                    ]),
                ];
            }

            $group = ['label' => 'Requests', 'items' => $items];
            $total = $match($db->table('hr_requests'))->countAllResults();

            if ($total > self::LIMIT) {
                $group['more'] = [
                    'text' => 'See all ' . $total . ' matching requests',
                    'url' => site_url('request') . '?' . http_build_query(['search' => $q]),
                ];
            }

            return $group;
        } catch (\Throwable $e) {
            log_message('error', 'Global search (requests) failed: ' . $e->getMessage());
            return null;
        }
    }

    /** TIA trainings: opens Annex D-1 for the training. */
    private function trainings(string $q): ?array
    {
        return $this->modelGroup(
            'TIA Trainings',
            new TrainingsModel(),
            ['training_code', 'training_title', 'training_dates', 'office'],
            $q,
            'training_title ASC',
            '*',
            static fn($r) => [
                'title' => $r['training_title'],
                'sub' => trim(($r['training_dates'] ?? '') . ' · ' . ($r['training_code'] ?? ''), ' ·'),
                'icon' => 'bi-mortarboard',
                'url' => site_url('tia-report') . '?' . http_build_query(['t' => $r['training_code']]),
            ],
            null,
            'trainings'
        );
    }

    private function employees(string $q): ?array
    {
        return $this->modelGroup(
            'Employees',
            new EmployeeModel(),
            ['employee_code', 'name', 'email', 'position', 'office'],
            $q,
            'name ASC',
            'id, employee_code, name, email, position, position_type, office',
            static fn($r) => [
                'title' => $r['name'],
                'sub' => trim(($r['position'] ?? '') . ' · ' . ($r['employee_code'] ?? ''), ' ·'),
                'badge' => $r['position_type'] ?? '',
                'icon' => 'bi-person-vcard',
                'url' => site_url('employees') . '?' . http_build_query(['q' => $r['name']]),
            ],
            site_url('employees'),
            'employees'
        );
    }

    private function superiors(string $q): ?array
    {
        return $this->modelGroup(
            'Superiors',
            new SuperiorModel(),
            ['name', 'employee_code', 'email', 'position'],
            $q,
            'name ASC',
            'id, employee_code, name, email, position',
            static fn($r) => [
                'title' => $r['name'],
                'sub' => trim(($r['position'] ?? '') . ' · ' . ($r['employee_code'] ?? ''), ' ·'),
                'icon' => 'bi-person-badge',
                'url' => site_url('superiors') . '?' . http_build_query(['q' => $r['name']]),
            ],
            site_url('superiors'),
            'superiors'
        );
    }

    /** Never selects the password column. */
    private function users(string $q): ?array
    {
        return $this->modelGroup(
            'Users',
            new UserModel(),
            ['fullname', 'username', 'email', 'role'],
            $q,
            'fullname ASC',
            'id, fullname, username, email, role, status',
            static fn($r) => [
                'title' => $r['fullname'],
                'sub' => $r['role'] . ' · ' . $r['username'],
                'badge' => $r['status'] ?? '',
                'icon' => 'bi-people',
                'url' => site_url('users') . '?' . http_build_query(['q' => $r['username']]),
            ],
            site_url('users'),
            'users'
        );
    }

    private function logs(string $q): ?array
    {
        try {
            $count = db_connect()->table('activity_logs')
                ->groupStart()
                ->like('username', $q)
                ->orLike('fullname', $q)
                ->orLike('description', $q)
                ->groupEnd()
                ->countAllResults();

            if ($count === 0) {
                return null;
            }

            return [
                'label' => 'Activity Logs',
                'items' => [
                    [
                        'title' => 'Search activity logs for “' . $q . '”',
                        'sub' => $count . ($count === 1 ? ' matching entry' : ' matching entries'),
                        'icon' => 'bi-clock-history',
                        'url' => site_url('activity-logs') . '?' . http_build_query(['q' => $q]),
                    ]
                ],
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Global search (logs) failed: ' . $e->getMessage());
            return null;
        }
    }

    // ------------------------------------------------------------------

    /**
     * Shared "LIKE any of these columns" search on a CI4 model.
     * $select is applied after counting so COUNT(*) is never mixed with a column list.
     */
    private function modelGroup(
        string $label,
        $model,
        array $fields,
        string $q,
        string $order,
        string $select,
        callable $map,
        ?string $moreUrl,
        string $moreWord
    ): ?array {
        try {
            $model->groupStart();
            foreach ($fields as $i => $field) {
                $i === 0 ? $model->like($field, $q) : $model->orLike($field, $q);
            }
            $model->groupEnd();

            $total = $model->countAllResults(false);
            if ($total === 0) {
                $model->resetQuery();
                return null;
            }

            $rows = $model->select($select)->orderBy($order)->limit(self::LIMIT)->findAll();

            $group = ['label' => $label, 'items' => array_map($map, $rows)];

            if ($moreUrl !== null && $total > self::LIMIT) {
                $group['more'] = [
                    'text' => 'See all ' . $total . ' matching ' . $moreWord,
                    'url' => $moreUrl . '?' . http_build_query(['q' => $q]),
                ];
            }

            return $group;
        } catch (\Throwable $e) {
            log_message('error', 'Global search (' . $label . ') failed: ' . $e->getMessage());
            return null;
        }
    }
}