<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HrRequestModel;
use App\Models\HrEmploymentHistoryModel;
use App\Models\UserModel;
use App\Libraries\Mailer;

class EportalController extends BaseController
{
    public function index()
    {
        return view('eportal/index');
    }

    public function service_record()
    {
        return view('hr_forms/hr_forms');
    }

    public function manual()
    {
        return view('dashboard/manual');
    }

    //===================================================
    // LOGIN / LOGOUT
    //===================================================

    public function login()
    {
        $session = session();

        // If already logged in, go directly to dashboard
        if ($session->get('logged_in') === true) {
            return redirect()->to($this->homeFor($session->get('role')));
        }

        // Prevent browser from caching the login page
        $response = service('response');
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');

        return view('admin/login');
    }

    public function login_process()
    {
        $model = new UserModel();

        $username = trim($this->request->getPost('username'));
        $password = $this->request->getPost('password');

        $user = $model->where('username', $username)->first();

        // Username not found
        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid username or password.');
        }

        // Account inactive
        if ($user['status'] !== 'ACTIVE') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        // Verify hashed password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid username or password.');
        }

        session()->regenerate(true);

        session()->set([
            'user_id' => $user['id'],
            'fullname' => $user['fullname'],
            'username' => $user['username'],
            'role' => $user['role'],
            'logged_in' => true,
        ]);

        return redirect()->to($this->homeFor($user['role']));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('login')->with('success', 'You have been logged out successfully.');
    }

    //===================================================
    // CLIENT REQUEST SUBMISSION (public form)
    //===================================================

    public function save()
    {
        return $this->processSubmission(site_url('service-record'), 'releasing_by');
    }

    //===================================================
    // MANUAL ENTRY BY ADMIN
    //===================================================

    public function manualsave()
    {
        return $this->processSubmission(site_url('dashboard'), 'released_by');
    }

    /**
     * Shared logic of save() and manualsave().
     * The only differences between the two were the redirect target and the
     * name of the "released by" column ('releasing_by' vs 'released_by').
     * Both are kept exactly as they were; fix $releasedByColumn once you
     * confirm the real column name in the hr_requests table.
     */
    private function processSubmission(string $redirectTo, string $releasedByColumn)
    {
        $model = new HrRequestModel();
        $historyModel = new HrEmploymentHistoryModel();

        // One reference number for the whole submission (all entries share this)
        $referenceNo = 'HR-' . date('YmdHis');

        // Service List
        $services = [
            'service1' => 'Service Record',
            'service2' => 'Certificate with Compensation',
            'service5' => 'Certificate of Employment',
            'service6' => 'Certification',
            'service7' => 'IPCR',
            'service8' => '201 Documents',
            'service9' => 'Others',
            'service10' => 'Performance Evaluation',
            'service11' => 'Payslip',
            'service12' => 'Contract of Service',
        ];

        // Employee category is ONE shared choice for the whole submission
        $employeeCategory = $this->request->getPost('employee_category');

        // Requester info is entered once, shared by every entry
        $fullname = trim($this->request->getPost('fullname') ?? '');
        $contactNumber = trim($this->request->getPost('contact_number') ?? '');
        $emailAddress = trim($this->request->getPost('email_address') ?? '');
        $dateRequested = date('Y-m-d');

        $entries = $this->request->getPost('entries');
        log_message('debug', 'ENTRIES RECEIVED: ' . print_r($entries, true));

        if (!is_array($entries) || count($entries) === 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'No request entries were submitted.');
        }

        $savedServiceLabels = []; // for the confirmation/admin emails
        $insertedAny = false;

        $monthNames = [
            '',
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        foreach ($entries as $entry) {

            $serviceKey = $entry['service_availed'] ?? '';
            $serviceAvailed = $services[$serviceKey] ?? 'Unknown Service';

            // Certification (checkbox array)
            $certification = $entry['certification'] ?? null;
            if (is_array($certification)) {
                $certification = implode(', ', $certification);
            }

            // IPCR Period
            $ipcrPeriod = trim(
                ($entry['ipcr_semester'] ?? '') . ' ' . ($entry['ipcr_year'] ?? '')
            );

            // Performance Evaluation Period
            $performanceEvaluationPeriod = trim(
                ($entry['performance_evaluation_semester'] ?? '') . ' ' . ($entry['performance_evaluation_year'] ?? '')
            );

            // Payslip Period
            $payslipMonth = $entry['payslip_month'] ?? '';
            $payslipYear = $entry['payslip_year'] ?? '';

            $payslipPeriod = '';
            if (!empty($payslipMonth) && !empty($payslipYear)) {
                $monthName = $monthNames[intval($payslipMonth)] ?? '';
                $payslipPeriod = trim($monthName . ' ' . $payslipYear);
            }

            // 201 Documents (checkbox array)
            $documents201 = $entry['documents_201'] ?? [];

            if (is_array($documents201)) {
                $documents201 = array_filter(array_map('trim', $documents201));

                $documents201Value = !empty($documents201)
                    ? implode(', ', $documents201)
                    : null;
            } else {
                $documents201Value = null;
            }

            $data = [
                'reference_no' => $referenceNo,
                'service_availed' => $serviceAvailed,
                'service_purpose' => trim($entry['service_purpose'] ?? ''),
                'fullname' => $fullname,
                'contact_number' => $contactNumber,
                'email_address' => $emailAddress,
                'employee_category' => $employeeCategory,
                'contract_of_service_year' => $entry['contract_of_service_year'] ?? null,
                'payslip_month' => $entry['payslip_month'] ?? null,
                'payslip_year' => $entry['payslip_year'] ?? null,
                'payslipPeriod' => $payslipPeriod,
                'period_of_service' => null,
                'position' => null,
                'office_division' => null,
                'certification' => $certification,
                'ipcr_period' => $ipcrPeriod,
                'performance_evaluation_period' => $performanceEvaluationPeriod,
                'documents_201' => $documents201Value,
                'specify_201' => $entry['specify_201'] ?? null,
                'other_request' => $entry['other_request'] ?? null,
                'date_requested' => $dateRequested,
                'status' => 'New Application',
                'remarks' => '',
                'date_received' => null,
                'received_by' => null,
                'date_released' => null,
                $releasedByColumn => null,
                'date_canceled' => null,
                'cancellation_reason' => null,
            ];

            if (!$model->insert($data)) {
                log_message('error', 'INSERT FAILED: ' . print_r($model->errors(), true));
                continue;
            }

            $insertedAny = true;
            $savedServiceLabels[] = $serviceAvailed;

            // Employment history for this entry (COS + Certificate of Employment only)
            if ($employeeCategory === 'cos' && $serviceKey === 'service5') {

                $periods = $entry['period_of_service'] ?? null;
                $positions = $entry['position'] ?? null;
                $offices = $entry['office_division'] ?? null;

                if (is_array($periods)) {

                    foreach ($periods as $key => $period) {

                        if (trim($period) === '') {
                            continue;
                        }

                        $historyModel->insert([
                            'reference_no' => $referenceNo,
                            'period_of_service' => trim($period),
                            'position' => trim($positions[$key] ?? ''),
                            'office_division' => trim($offices[$key] ?? ''),
                        ]);
                    }
                } elseif (!empty($periods)) {

                    // Backward compatibility (single employment history)
                    $historyModel->insert([
                        'reference_no' => $referenceNo,
                        'period_of_service' => trim($periods),
                        'position' => trim($positions),
                        'office_division' => trim($offices),
                    ]);
                }
            }
        }

        if (!$insertedAny) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unable to submit your request.');
        }

        // One email for the whole reference number
        $mailer = new Mailer();

        $servicesSummary = implode(', ', array_unique($savedServiceLabels));

        // Client Email
        $clientEmail = view('emails/hr_request_confirmation', [
            'fullname' => $fullname,
            'reference_no' => $referenceNo,
            'service' => $servicesSummary,
            'date_requested' => $dateRequested,
        ]);

        $mailer->send(
            $emailAddress,
            'HR Request Submitted Successfully',
            $clientEmail
        );

        // Email to HR administrators
        $userModel = new UserModel();

        $hrAdmins = $userModel
            ->where('role', 'HR Administrator')
            ->where('status', 'ACTIVE')
            ->findAll();

        foreach ($hrAdmins as $admin) {

            $adminEmail = view('emails/hr_request_admin_notification', [
                'reference_no' => $referenceNo,
                'fullname' => $fullname,
                'service' => $servicesSummary,
                'date_requested' => $dateRequested,
            ]);

            $mailer->send(
                $admin['email'],
                'New HR Request Submitted',
                $adminEmail
            );
        }

        return redirect()
            ->to($redirectTo)
            ->with('success', 'Your request has been submitted successfully.')
            ->with('reference_no', $referenceNo);
    }

    /** Where each role lands after login. */
    private function homeFor(?string $role): string
    {
        return strtoupper(trim((string) $role)) === 'SUPERIOR'
            ? site_url('tia-superior/monitoring')
            : site_url('dashboard');
    }
}