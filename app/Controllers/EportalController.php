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
    public function request()
    {
        $requestModel = new HrRequestModel();
        $query = $requestModel;

        if ($year = $this->request->getGet('year')) {
            $query->like('date_requested', $year, 'after');
        }

        if ($status = $this->request->getGet('status')) {
            $query->like('status', $status);
        }

        if ($service = $this->request->getGet('service_availed')) {
            $query->like('service_availed', $service);
        }

        if ($unit = $this->request->getGet('office_division')) {
            $query->like('office_division', $unit);
        }

        if ($dateRequested = $this->request->getGet('date_requested')) {
            $query->like('date_requested', $dateRequested, 'after');
        }

        $data['requests'] = $query
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $data['pager'] = $requestModel->pager;

        return view('dashboard/request', $data);
    }

    public function dashboard()
    {
        $requestModel = new HrRequestModel();

        $data = [
            'new_application' => $requestModel->where('status', 'New Application')->countAllResults(),
            'processing' => $requestModel->where('status', 'Processing')->countAllResults(),
            'completed' => $requestModel->where('status', 'Completed')->countAllResults(),
            'for_released' => $requestModel->where('status', 'For Released')->countAllResults(),
            'rejected' => $requestModel->where('status', 'Rejected')->countAllResults(),
        ];

        // Load recent requests
        $data['requests'] = $requestModel
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $data['pager'] = $requestModel->pager;

        return view('dashboard/dashboard', $data);
    }
    public function service_record()
    {
        return view('hr_forms/hr_forms');
    }

    public function login()
    {
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

        // Regenerate session ID to prevent session fixation
        session()->regenerate();

        session()->set([
            'user_id' => $user['id'],
            'fullname' => $user['fullname'],
            'username' => $user['username'],
            'role' => $user['role'],
            'logged_in' => true,
        ]);

        return redirect()->to(site_url('dashboard'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('login')->with('success', 'You have been logged out successfully.');
    }

    public function save()
    {
        $model = new HrRequestModel();
        $historyModel = new HrEmploymentHistoryModel();

        // Generate Reference Number
        $referenceNo = 'HR-' . date('YmdHis');

        // Service List
        $services = [
            'service1' => 'Service Record',
            'service2' => 'Certificate with Compensation',
            'service3' => 'Loan Assistance',
            'service4' => 'Inquiry of Leave Balance',
            'service5' => 'Certificate of Employment',
            'service6' => 'Certification',
            'service7' => 'IPCR',
            'service8' => '201 Documents',
            'service9' => 'Others',
        ];

        $serviceAvailed = $services[$this->request->getPost('service_availed')] ?? 'Unknown Service';

        // Certification
        $certification = $this->request->getPost('certification');

        if (is_array($certification)) {
            $certification = implode(', ', $certification);
        }

        // IPCR Period
        $ipcrPeriod =
            $this->request->getPost('ipcr_from_month') . ' ' .
            $this->request->getPost('ipcr_from_year') .
            ' to ' .
            $this->request->getPost('ipcr_to_month') . ' ' .
            $this->request->getPost('ipcr_to_year');

        // Main Request Data
        $data = [

            'reference_no' => $referenceNo,
            'service_availed' => $serviceAvailed,
            'service_purpose' => trim($this->request->getPost('service_purpose') ?? ''),
            'fullname' => trim($this->request->getPost('fullname') ?? ''),
            'contact_number' => trim($this->request->getPost('contact_number') ?? ''),
            'email_address' => trim($this->request->getPost('email_address') ?? ''),
            'employee_category' => $this->request->getPost('employee_category'),

            // Employment history is stored in another table
            'period_of_service' => null,
            'position' => null,
            'office_division' => null,

            'certification' => $certification,
            'ipcr_period' => $ipcrPeriod,
            'specify_201' => $this->request->getPost('specify_201'),
            'other_request' => $this->request->getPost('other_request'),

            'date_requested' => date('Y-m-d'),

            'status' => 'New Application',
            'remarks' => '',

            'date_received' => null,
            'received_by' => null,
            'date_released' => null,
            'released_by' => null,
        ];

        // Save Main Request
        if ($model->insert($data)) {

            /*
            |--------------------------------------------------------------------------
            | SAVE EMPLOYMENT HISTORY (COS ONLY)
            |--------------------------------------------------------------------------
            */

            if ($this->request->getPost('employee_category') === 'cos') {

                $periods = $this->request->getPost('period_of_service');
                $positions = $this->request->getPost('position');
                $offices = $this->request->getPost('office_division');

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

                } else {

                    // Backward compatibility (single employment history)

                    if (!empty($periods)) {

                        $historyModel->insert([
                            'reference_no' => $referenceNo,
                            'period_of_service' => trim($periods),
                            'position' => trim($positions),
                            'office_division' => trim($offices),
                        ]);

                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | SEND EMAIL USING PHPMailer
            |--------------------------------------------------------------------------
            */

            $mailer = new Mailer();

            // Client Email
            $clientEmail = view(
                'emails/hr_request_confirmation',
                [
                    'fullname' => $data['fullname'],
                    'reference_no' => $referenceNo,
                    'service' => $serviceAvailed,
                    'date_requested' => $data['date_requested']
                ]
            );

            $mailer->send(
                $data['email_address'],
                'HR Request Submitted Successfully',
                $clientEmail
            );

            /*
            |--------------------------------------------------------------------------
            | SEND EMAIL TO HR ADMINISTRATORS
            |--------------------------------------------------------------------------
            */

            $userModel = new UserModel();

            $hrAdmins = $userModel
                ->where('role', 'HR Administrator')
                ->where('status', 'ACTIVE')
                ->findAll();

            foreach ($hrAdmins as $admin) {

                $adminEmail = view(
                    'emails/hr_request_admin_notification',
                    [
                        'reference_no' => $referenceNo,
                        'fullname' => $data['fullname'],
                        'service' => $serviceAvailed,
                        'date_requested' => $data['date_requested']
                    ]
                );

                $mailer->send(
                    $admin['email'],
                    'New HR Request Submitted',
                    $adminEmail
                );
            }

            return redirect()
                ->back()
                ->with('success', 'Your request has been submitted successfully.')
                ->with('reference_no', $referenceNo);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Unable to submit your request.');
    }

    public function users()
    {
        $model = new UserModel();

        $data = [
            'users' => $model->paginate(10),
            'pager' => $model->pager
        ];

        // Pass $users to the view
        return view('dashboard/users', $data);
    }
    //===================================================
    // ADD USER
    //===================================================

    public function storeUser()
    {
        $model = new UserModel();

        $rules = [
            'fullname' => 'required|min_length[3]',
            'username' => 'required|min_length[4]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model->insert([
            'fullname' => trim($this->request->getPost('fullname')),
            'username' => trim($this->request->getPost('username')),
            'email' => trim($this->request->getPost('email')),
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ]);

        return redirect()->back()->with('success', 'User added successfully.');
    }
    //===================================================
    // UPDATE USER
    //===================================================

    public function updateUser($id)
    {
        $model = new UserModel();

        $username = trim($this->request->getPost('username'));

        // Check if another user already uses this username
        $existing = $model->where('username', $username)
            ->where('id !=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username already exists.');
        }

        $data = [
            'fullname' => trim($this->request->getPost('fullname')),
            'username' => trim($this->request->getPost('username')),
            'email' => trim($this->request->getPost('email')),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ];

        $password = trim($this->request->getPost('password'));

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $model->update($id, $data);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    //===================================================
    // DELETE USER
    //===================================================

    public function deleteUser($id)
    {
        $model = new UserModel();

        $model->delete($id);

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function updateRequest($id)
    {
        $requestModel = new HrRequestModel();

        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'contact_number' => $this->request->getPost('contact_number'),
            'email_address' => $this->request->getPost('email_address'),
            'date_requested' => $this->request->getPost('date_requested'),
            'status' => $this->request->getPost('status'),
            'remarks' => $this->request->getPost('remarks'),
        ];

        $requestModel->update($id, $data);

        return redirect()
            ->to(site_url('request'))
            ->with('success', 'HR request updated successfully.');
    }

    public function deleteRequest($id)
    {
        $requestModel = new HrRequestModel();

        $request = $requestModel->find($id);

        if (!$request) {
            return redirect()
                ->to(site_url('request'))
                ->with('error', 'Request not found.');
        }

        $requestModel->delete($id);

        return redirect()
            ->to(site_url('request'))
            ->with('success', 'HR request deleted successfully.');
    }

    public function saveRequest()
    {
        $requestModel = new HrRequestModel();

        $data = $this->request->getPost([
            'service_availed',
            'fullname',
            'contact_number',
            'email_address',
            'employee_category',
            'period_of_service',
            'position',
            'office_division',
            'ipcr_period',
            'specify_201',
            'date_requested',
        ]);

        $data['reference_no'] = 'HR-' . date('YmdHis');
        $data['status'] = 'New Application';
        $data['certification'] = implode(', ', $this->request->getPost('certification') ?? []);

        $requestModel->insert($data);

        return redirect()
            ->to(site_url('request'))
            ->with('success', 'HR request submitted successfully.');
    }

    public function reports()
    {
        $model = new HrRequestModel();

        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $service = $this->request->getGet('service');
        $search = $this->request->getGet('q');

        if (!empty($search)) {
            $model->groupStart()
                ->like('fullname', $search)
                ->orLike('reference_no', $search)
                ->groupEnd();
        }

        if (!empty($year)) {
            $model->where('YEAR(date_requested)', $year);
        }

        if (!empty($month)) {
            $model->where('MONTH(date_requested)', $month);
        }

        if (!empty($service)) {
            $model->where('service_availed', $service);
        }

        $data = [
            'requests' => $model->orderBy('id', 'DESC')->paginate(10),
            'pager' => $model->pager,
            'year' => $year,
            'month' => $month,
            'service' => $service,
            'search' => $search,
        ];

        return view('dashboard/reports', $data);
    }

}