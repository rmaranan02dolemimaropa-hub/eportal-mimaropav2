<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;

/** Activity Logs page (IT Administrator only): search, filter, page and export. */
class ActivityLogController extends BaseController
{
    private const PER_PAGE = 25;
    private const EXPORT_MAX = 10000;

    public function index()
    {



        if (!$this->allowed()) {
            return redirect()->to(base_url('unauthorized'));
        }

        $db = db_connect();
        $filters = $this->filters();

        $total = $this->query($db, $filters)->countAllResults();
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min(max(1, (int) $this->request->getGet('page')), $pages);

        $logs = $this->query($db, $filters)
            ->orderBy('id', 'DESC')
            ->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)
            ->get()->getResultArray();

        return view('dashboard/activity_logs', [
            'logs' => $logs,
            'filters' => $filters,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'perPage' => self::PER_PAGE,
            'modules' => array_column($db->query('SELECT DISTINCT module FROM activity_logs ORDER BY module')->getResultArray(), 'module'),
            'actions' => array_column($db->query('SELECT DISTINCT action FROM activity_logs ORDER BY action')->getResultArray(), 'action'),
        ]);
    }

    public function export()
    {
        if (!$this->allowed()) {
            return redirect()->to(base_url('unauthorized'));
        }

        $rows = $this->query(db_connect(), $this->filters())
            ->orderBy('id', 'DESC')
            ->limit(self::EXPORT_MAX)
            ->get()->getResultArray();

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF");   // UTF-8 BOM so Excel opens it correctly
        fputcsv($out, ['Date/Time', 'Username', 'Full name', 'Role', 'Module', 'Action', 'Description', 'IP address']);

        foreach ($rows as $r) {
            fputcsv($out, array_map([$this, 'csvSafe'], [
                $r['created_at'],
                $r['username'],
                $r['fullname'],
                $r['role'],
                $r['module'],
                $r['action'],
                $r['description'],
                $r['ip_address'],
            ]));
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        ActivityLogger::record('LOG_EXPORT', 'LOGS', 'Exported ' . count($rows) . ' activity log entries');

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="activity-logs-' . date('Ymd-His') . '.csv"')
            ->setBody($csv);
    }

    // ------------------------------------------------------------------

    private function allowed(): bool
    {
        return strtoupper(trim((string) session()->get('role'))) === 'IT ADMINISTRATOR';
    }

    /** Cleaned filter values from the query string. */
    private function filters(): array
    {
        $get = $this->request->getGet();
        $date = static fn($v) => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $v) ? (string) $v : '';

        return [
            'q' => mb_substr(trim((string) ($get['q'] ?? '')), 0, 100),
            'module' => mb_substr(trim((string) ($get['module'] ?? '')), 0, 30),
            'action' => mb_substr(trim((string) ($get['action'] ?? '')), 0, 60),
            'date_from' => $date($get['date_from'] ?? ''),
            'date_to' => $date($get['date_to'] ?? ''),
        ];
    }

    private function query($db, array $f)
    {
        $b = $db->table('activity_logs');

        if ($f['q'] !== '') {
            $b->groupStart()
                ->like('username', $f['q'])
                ->orLike('fullname', $f['q'])
                ->orLike('description', $f['q'])
                ->orLike('ip_address', $f['q'])
                ->groupEnd();
        }
        if ($f['module'] !== '') {
            $b->where('module', $f['module']);
        }
        if ($f['action'] !== '') {
            $b->where('action', $f['action']);
        }
        if ($f['date_from'] !== '') {
            $b->where('created_at >=', $f['date_from'] . ' 00:00:00');
        }
        if ($f['date_to'] !== '') {
            $b->where('created_at <=', $f['date_to'] . ' 23:59:59');
        }

        return $b;
    }

    /** Stop spreadsheet formulas: failed-login rows contain text typed by strangers. */
    private function csvSafe($value): string
    {
        $value = (string) $value;

        return ($value !== '' && strpbrk($value[0], "=+-@\t\r") !== false) ? "'" . $value : $value;
    }
}