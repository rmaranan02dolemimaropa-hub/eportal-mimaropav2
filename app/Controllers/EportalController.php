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
        $requestModel = new HrRequestModel();

        $data = [
            'pending' => $requestModel->where('status', 'Pending')->countAllResults(),
            'processing' => $requestModel->where('status', 'Processing')->countAllResults(),
            'completed' => $requestModel->where('status', 'Completed')->countAllResults(),
            'released' => $requestModel->where('status', 'Released')->countAllResults(),
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

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid username or password.');
        }

        // Check if account is active
        if ($user['status'] !== 'ACTIVE') {
            return redirect()->back()->with('error', 'Your account is inactive.');
        }

        // Verify hashed password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid username or password.');
        }

        session()->set([
            'user_id' => $user['id'],
            'fullname' => $user['fullname'],
            'username' => $user['username'],
            'role' => $user['role'],
            'logged_in' => true,
        ]);

        return redirect()->to('dashboard');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('login')->with('success', 'You have been logged out successfully.');
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
            'reference_no' => $referenceNo,
            'service_availed' => $serviceAvailed,
            'fullname' => $this->request->getPost('fullname'),
            'contact_number' => $this->request->getPost('contact_number'),
            'email_address' => $this->request->getPost('email_address'),
            'employee_category' => $this->request->getPost('employee_category'),
            'period_of_service' => $this->request->getPost('period_of_service'),
            'position' => $this->request->getPost('position'),
            'office_division' => $this->request->getPost('office_division'),
            'certification' => $certification,
            'ipcr_period' => $this->request->getPost('ipcr_period'),
            'specify_201' => $this->request->getPost('specify_201'),
            'date_requested' => $dateRequested,
            'status' => 'Pending',
            'remarks' => '',
            'date_received' => null,
            'received_by' => null,
            'date_released' => null,
            'released_by' => null,
        ];

        // Save Request
        if ($model->insert($data)) {

            return redirect()->back()
                ->with('success', 'Your request has been submitted successfully.')
                ->with('reference_no', $referenceNo);
        } else {

            dd([
                'Validation Errors' => $model->errors(),
                'Database Error' => $model->db->error(),
                'POST Data' => $this->request->getPost(),
                'Data to Insert' => $data,
            ]);
        }
    }

    //===================================================
    // USER LIST
    //===================================================

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

        $model->save([
            'fullname' => $this->request->getPost('fullname'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status')
        ]);

        return redirect()->back()->with('success', 'User added successfully.');
    }

    //===================================================
    // UPDATE USER
    //===================================================

    public function updateUser($id)
    {
        $model = new UserModel();

        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'username' => $this->request->getPost('username'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status')
        ];

        if (!empty($this->request->getPost('password'))) {
            $data['password'] = password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            );
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
}
