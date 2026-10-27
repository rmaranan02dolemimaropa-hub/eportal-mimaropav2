<?php

namespace App\Filters;

use App\Libraries\ActivityLogger;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Global "after" filter: records the important actions (saves, edits, deletes,
 * imports, exports) without touching each controller. Only the route and its id
 * are logged, never the submitted form data.
 *
 * Login, logout and two-step verification are logged explicitly in their own
 * code (see ActivityLogger), so they are not listed here.
 */
class ActivityFilter implements FilterInterface
{
    /**
     * [HTTP method, uri pattern (fnmatch), ACTION, MODULE, description, public?]
     * {id}   = the last part of the URL   {user} = "Full Name (username)"
     * public = true: also logged when nobody is signed in (client forms).
     */
    private const RULES = [
        // Users
        ['POST', 'users/store', 'USER_CREATE', 'USERS', 'Created a user account'],
        ['POST', 'users/update/*', 'USER_UPDATE', 'USERS', 'Updated user account {user}'],
        ['POST', 'users/deactivate/*', 'USER_DEACTIVATE', 'USERS', 'Deactivated user account {user}'],
        ['POST', 'users/activate/*', 'USER_ACTIVATE', 'USERS', 'Activated user account {user}'],
        ['POST', 'users/reset-mfa/*', 'MFA_RESET', 'USERS', 'Reset two-step verification for {user}'],

        // HR requests
        ['POST', 'service-record/manualsave', 'REQUEST_MANUAL_ENTRY', 'REQUESTS', 'Added a request manually'],
        ['POST', 'service-record/save', 'REQUEST_SUBMITTED', 'REQUESTS', 'Client submitted an HR request', true],
        ['POST', 'request/save', 'REQUEST_SAVE', 'REQUESTS', 'Saved a request'],
        ['POST', 'request/start-processing', 'REQUEST_PROCESSING', 'REQUESTS', 'Started processing requests'],
        ['POST', 'request/update/*', 'REQUEST_UPDATE', 'REQUESTS', 'Updated request #{id}'],
        ['POST', 'request/cancel/*', 'REQUEST_CANCEL', 'REQUESTS', 'Cancelled request #{id}'],

        // Feedback
        ['POST', 'feedback/save', 'FEEDBACK_SUBMITTED', 'FEEDBACK', 'Client submitted HR feedback', true],
        ['POST', 'submit-csm', 'CSM_SUBMITTED', 'FEEDBACK', 'Client submitted a satisfaction survey', true],

        // Employees
        ['POST', 'employees/store', 'EMPLOYEE_CREATE', 'EMPLOYEES', 'Added an employee'],
        ['POST', 'employees/update/*', 'EMPLOYEE_UPDATE', 'EMPLOYEES', 'Updated employee #{id}'],
        ['POST', 'employees/delete/*', 'EMPLOYEE_DELETE', 'EMPLOYEES', 'Deleted employee #{id}'],
        ['POST', 'employees/import', 'EMPLOYEE_IMPORT', 'EMPLOYEES', 'Imported employees from a file'],
        ['GET', 'employees/export', 'EMPLOYEE_EXPORT', 'EMPLOYEES', 'Exported employees'],

        // Superiors
        ['POST', 'superiors/store', 'SUPERIOR_CREATE', 'SUPERIORS', 'Added a superior'],
        ['POST', 'superiors/quick-add', 'SUPERIOR_CREATE', 'SUPERIORS', 'Quick-added a superior'],
        ['POST', 'superiors/update/*', 'SUPERIOR_UPDATE', 'SUPERIORS', 'Updated superior #{id}'],
        ['POST', 'superiors/delete/*', 'SUPERIOR_DELETE', 'SUPERIORS', 'Deleted superior #{id}'],

        // Training Impact Assessment
        ['POST', 'tia/store-training', 'TRAINING_CREATE', 'TIA', 'Created a training'],
        ['POST', 'tia/update-training/*', 'TRAINING_UPDATE', 'TIA', 'Updated training {id}'],
        ['POST', 'tia/delete-training/*', 'TRAINING_DELETE', 'TIA', 'Deleted training {id}'],
        ['POST', 'tia/update-status/*', 'TRAINING_STATUS', 'TIA', 'Changed status of training {id}'],
        ['POST', 'tia/import-trainings', 'TRAINING_IMPORT', 'TIA', 'Imported trainings from a file'],
        ['POST', 'tia/add-participant', 'PARTICIPANT_ADD', 'TIA', 'Added a participant'],
        ['POST', 'tia/add-participants-bulk', 'PARTICIPANT_BULK_ADD', 'TIA', 'Added participants in bulk'],
        ['POST', 'tia/update-participant/*', 'PARTICIPANT_UPDATE', 'TIA', 'Updated participant #{id}'],
        ['POST', 'tia/delete-participant/*', 'PARTICIPANT_DELETE', 'TIA', 'Deleted participant #{id}'],
        ['POST', 'tia/send-form-emails', 'TIA_EMAILS_SENT', 'TIA', 'Sent assessment form emails'],
        ['POST', 'tia/notify-superior', 'SUPERIOR_NOTIFIED', 'TIA', 'Notified a superior'],
        ['POST', 'tia/send-superior-emails', 'SUPERIOR_EMAILS_SENT', 'TIA', 'Sent superior assessment emails'],
        ['POST', 'tia-report-annex-j/save', 'ANNEX_J_SAVE', 'TIA', 'Saved Annex J'],
        ['POST', 'tia_store', 'TIA_ANNEX_D_SUBMITTED', 'TIA', 'Participant submitted Annex D', true],
        ['POST', 'tia_superior_store', 'TIA_ANNEX_E_SUBMITTED', 'TIA', 'Superior submitted Annex E', true],
        ['GET', 'tia/export-participants/*', 'TIA_EXPORT', 'TIA', 'Exported participants of {id}'],
        ['GET', 'tia/export-trainings', 'TIA_EXPORT', 'TIA', 'Exported trainings'],
        ['GET', 'tia-report/export', 'TIA_EXPORT', 'TIA', 'Exported Annex D report'],
        ['GET', 'tia-report-superior/export', 'TIA_EXPORT', 'TIA', 'Exported Annex E report'],
        ['GET', 'tia-report-bulk/export', 'TIA_EXPORT', 'TIA', 'Exported bulk TIA report'],

        // Reports
        ['GET', 'reports/print', 'REPORT_PRINT', 'REPORTS', 'Printed the HR request report'],
        ['GET', 'export/hr-request', 'REPORT_EXPORT', 'REPORTS', 'Exported the HR request report'],
        ['GET', 'export/reports-pdf', 'REPORT_EXPORT', 'REPORTS', 'Exported reports (PDF)'],
        ['GET', 'export/reports-csv', 'REPORT_EXPORT', 'REPORTS', 'Exported reports (CSV)'],
        ['GET', 'exports/satisfaction-excel*', 'REPORT_EXPORT', 'REPORTS', 'Exported satisfaction report (Excel)'],
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // nothing to do before the request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $method = strtoupper((string) $request->getMethod());
        $uri = trim(uri_string(), '/');

        foreach (self::RULES as $rule) {
            [$m, $pattern, $action, $module, $description] = $rule;
            $public = $rule[5] ?? false;

            if ($m !== $method || !fnmatch($pattern, $uri)) {
                continue;
            }

            $actor = null;
            if ((int) session()->get('user_id') <= 0) {
                if (!$public) {
                    return;     // not signed in on a staff-only route: nothing worth logging
                }
                $actor = ['username' => '(public)'];
            }

            $target = str_ends_with($pattern, '*') ? substr($uri, strlen(rtrim($pattern, '*'))) : '';
            $text = str_replace(
                ['{id}', '{user}'],
                [$target, str_contains($description, '{user}') ? $this->userLabel($target) : ''],
                $description
            );

            if ($this->failed($response)) {
                $action .= '_FAILED';
                $text .= ' (did not succeed)';
            }

            ActivityLogger::record($action, $module, $text, $actor);
            return;
        }
    }

    /** "Full Name (username)" for a numeric user id, never any other column. */
    private function userLabel(string $id): string
    {
        if (!ctype_digit($id)) {
            return '#' . $id;
        }

        $u = db_connect()->table('users')->select('fullname, username')->where('id', (int) $id)->get()->getRowArray();

        return $u ? "{$u['fullname']} ({$u['username']})" : '#' . $id;
    }

    /** Best guess that the action did not go through. */
    private function failed(ResponseInterface $response): bool
    {
        if ($response->getStatusCode() >= 400) {
            return true;
        }

        $s = session();
        if ($s->getFlashdata('error') || $s->getFlashdata('errors')) {
            return true;
        }

        if (stripos($response->getHeaderLine('Content-Type'), 'json') !== false) {
            $body = (string) $response->getBody();
            if ($body !== '' && strlen($body) < 100000) {
                $j = json_decode($body, true);
                if (
                    is_array($j) && (
                        ($j['success'] ?? true) === false
                        || in_array($j['status'] ?? '', ['error', 'fail', 'failed'], true)
                        || !empty($j['error'])
                    )
                ) {
                    return true;
                }
            }
        }

        return false;
    }
}