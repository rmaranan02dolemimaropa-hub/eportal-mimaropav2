<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HrRequestModel;
use App\Models\UserModel;

class EportalController extends BaseController
{
    public function index()
    {
        return view('eportal/index');
    }

    public function dashboard()
    {
        return view('dashboard/dashboard');
    }

    public function service_record()
    {
        return view('hr_forms/hr_forms');
    }
    public function users()
    {
        return view('dashboard/users');
    }
    public function login()
    {
        return view('admin/login');
    }

    public function login_process()
    {
        $session = session();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $model = new UserModel();

        $user = $model
            ->where('username', $username)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid username.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid password.');
        }

        if ($user['status'] != 1) {
            return redirect()->back()->with('error', 'Account disabled.');
        }

        $session->set([
            'user_id'  => $user['id'],
            'fullname' => $user['fullname'],
            'role'     => $user['role'],
            'logged_in'=> true
        ]);

        if ($user['role'] == 'IT ADMINISTRATOR') {
            return redirect()->to('/it/dashboard');
        }

        if ($user['role'] == 'HR ADMINISTRATOR') {
            return redirect()->to('/hr/dashboard');
        }

        return redirect()->to('/');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/');
    }

    public function save()
    {
        $model = new HrRequestModel();

        // Generate Reference Number
        $referenceNo = 'HR-' . date('YmdHis');

        // Convert service code to readable service name
        $services = [
            'service1' => 'Service Record',
            'service2' => 'Certificate with Compensation',
            'service3' => 'Loan Assistance',
            'service4' => 'Inquiry of Leave Balance',
            'service5' => 'Certificate of Employment',
            'service6' => 'Certification',
            'service7' => 'IPCR',
            'service8' => '201 Documents',
        ];

        $serviceAvailed = $services[$this->request->getPost('service_availed')] ?? 'Unknown Service';

        // Get Certification (multiple checkbox)
        $certification = $this->request->getPost('certification');

        if (is_array($certification)) {
            $certification = implode(', ', $certification);
        }

        // Get Date Requested from the active service
        $dateRequested = null;

        $dateFields = [
            'service_record_date',
            'certificate_date',
            'loan_date',
            'leave_date',
            'cos_date',
            'permanent_date',
            'certification_date',
            'ipcr_date',
            'documents_date'
        ];

        foreach ($dateFields as $field) {
            $value = $this->request->getPost($field);

            if (!empty($value)) {
                $dateRequested = $value;
                break;
            }
        }

        // Prepare data for insertion
        $data = [
            'reference_no'      => $referenceNo,
            'service_availed'   => $serviceAvailed,
            'fullname'          => $this->request->getPost('fullname'),
            'contact_number'    => $this->request->getPost('contact_number'),
            'email_address'     => $this->request->getPost('email_address'),
            'employee_category' => $this->request->getPost('employee_category'),
            'period_of_service' => $this->request->getPost('period_of_service'),
            'position'          => $this->request->getPost('position'),
            'office_division'   => $this->request->getPost('office_division'),
            'certification'     => $certification,
            'ipcr_period'       => $this->request->getPost('ipcr_period'),
            'specify_201'       => $this->request->getPost('specify_201'),
            'date_requested'    => $dateRequested,
            'status'            => 'Pending',
            'remarks'           => '',
            'date_received'     => null,
            'received_by'       => null,
            'date_released'     => null,
            'released_by'       => null,
        ];

        // Save Request
        if ($model->insert($data)) {

            return redirect()->back()
                ->with('success', 'Your request has been submitted successfully.')
                ->with('reference_no', $referenceNo);
        } else {

            dd([
                'Validation Errors' => $model->errors(),
                'Database Error'    => $model->db->error(),
                'POST Data'         => $this->request->getPost(),
                'Data to Insert'    => $data,
            ]);
        }
    }
}
