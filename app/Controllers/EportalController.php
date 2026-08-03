<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HrRequestModel;
use App\Models\HrEmploymentHistoryModel;
use App\Models\ClientSatisfactionModel;
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

        $historyModel = new HrEmploymentHistoryModel();

        $requestModel = new HrRequestModel();

        $query = $requestModel
            ->select('hr_requests.*, client_satisfaction.id AS feedback_id')
            ->join(
                'client_satisfaction',
                'client_satisfaction.reference_no = hr_requests.reference_no',
                'left'
            );




        // Search
        if ($search = $this->request->getGet('search')) {

            $query->groupStart()
                ->like('reference_no', $search)
                ->orLike('fullname', $search)
                ->orLike('email_address', $search)
                ->orLike('contact_number', $search)
                ->orLike('service_availed', $search)
                ->orLike('status', $search)
                ->groupEnd();

        }


        // Filters
        if ($year = $this->request->getGet('year')) {
            $query->like('date_requested', $year, 'after');
        }


        if ($status = $this->request->getGet('status')) {
            $query->where('status', $status);
        }


        if ($service = $this->request->getGet('service_availed')) {
            $query->where('service_availed', $service);
        }


        $requests = $query
            ->orderBy("
        CASE
            WHEN status = 'New Application' THEN 1
            WHEN status = 'Processing' THEN 2
            WHEN status = 'For Released' THEN 3
            WHEN status = 'Completed' THEN 4
            WHEN status = 'Rejected' THEN 5
            ELSE 6
        END
    ", '', false)
            ->orderBy("
        CASE
            WHEN status = 'New Application' THEN date_requested
            WHEN status = 'Processing' THEN date_processing
            WHEN status = 'For Released' THEN date_released
            WHEN status = 'Completed' THEN date_completed
            WHEN status = 'Rejected' THEN date_rejected
            ELSE date_requested
        END
    ", 'DESC', false)
            ->paginate(10);



        foreach ($requests as &$row) {

            $row['employment_history'] = $historyModel
                ->where('reference_no', $row['reference_no'])
                ->findAll();

        }


        return view('dashboard/request', [
            'requests' => $requests,
            'pager' => $requestModel->pager
        ]);
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
            ->orderBy("
        CASE
            WHEN status = 'New Application' THEN 1
            WHEN status = 'Processing' THEN 2
            WHEN status = 'For Released' THEN 3
            WHEN status = 'Completed' THEN 4
            WHEN status = 'Rejected' THEN 5
            ELSE 6
        END
    ", '', false)
            ->orderBy("
        CASE
            WHEN status = 'New Application' THEN date_requested
            WHEN status = 'Processing' THEN date_processing
            WHEN status = 'For Released' THEN date_released
            WHEN status = 'Completed' THEN date_completed
            WHEN status = 'Rejected' THEN date_rejected
        END
    ", 'DESC', false)
            ->paginate(10);

        $data['pager'] = $requestModel->pager;

        $data['new_today'] = $requestModel
            ->where('status', 'New Application')
            ->where('DATE(date_requested)', date('Y-m-d'))
            ->countAllResults();

        $data['processing_today'] = $requestModel
            ->where('status', 'Processing')
            ->where('DATE(date_processing)', date('Y-m-d'))
            ->countAllResults();

        $data['released_today'] = $requestModel
            ->where('status', 'For Released')
            ->where('DATE(date_released)', date('Y-m-d'))
            ->countAllResults();

        $data['completed_month'] = $requestModel
            ->where('status', 'Completed')
            ->where('YEAR(date_completed)', date('Y'))
            ->where('MONTH(date_completed)', date('m'))
            ->countAllResults();

        $data['rejected_month'] = $requestModel
            ->where('status', 'Rejected')
            ->where('YEAR(date_rejected)', date('Y'))
            ->where('MONTH(date_rejected)', date('m'))
            ->countAllResults();

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

    public function feedback()
    {
        $csmModel = new ClientSatisfactionModel();

        $data['feedbacks'] = $csmModel
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $data['pager'] = $csmModel->pager;

        return view('dashboard/feedback', $data);
    }

    /**
     * Print CSM Feedback
     */
    public function printEnglish($id)
    {
        $csmModel = new ClientSatisfactionModel();

        $data['feedback'] = $csmModel->find($id);

        if (!$data['feedback']) {
            return redirect()->back()->with('error', 'Feedback not found.');
        }

        return view('exports/feedback_print_english', $data);
    }

    public function printTagalog($id)
    {
        $csmModel = new ClientSatisfactionModel();

        $data['feedback'] = $csmModel->find($id);

        if (!$data['feedback']) {
            return redirect()->back()->with('error', 'Feedback not found.');
        }

        return view('exports/feedback_print_tagalog', $data);
    }

    public function updateRequest($id)
    {
        $requestModel = new HrRequestModel();

        $status = $this->request->getPost('status');


        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'contact_number' => $this->request->getPost('contact_number'),
            'email_address' => $this->request->getPost('email_address'),
            'date_requested' => $this->request->getPost('date_requested'),
            'status' => $status,
            'remarks' => $this->request->getPost('remarks'),
        ];


        // Update status date automatically
        switch ($status) {

            case 'Processing':

                $data['date_processing'] = date('Y-m-d H:i:s');

                break;


            case 'For Released':

                $data['date_released'] = date('Y-m-d H:i:s');

                break;


            case 'Completed':

                $data['date_completed'] = date('Y-m-d H:i:s');

                break;


            case 'Rejected':

                $data['date_rejected'] = date('Y-m-d H:i:s');

                break;

        }


        $requestModel->update($id, $data);


        //===================================================
// SEND STATUS UPDATE EMAIL TO CLIENT
//===================================================

        $request = $requestModel->find($id);

        if ($request && !empty($request['email_address'])) {

            $mailer = new Mailer();

            switch ($status) {


                case 'Processing':

                    $subject = "Your HR Request is Now Being Processed";

                    $message = view('emails/hr_status_update', [
                        'fullname' => $request['fullname'],
                        'reference_no' => $request['reference_no'],
                        'service' => $request['service_availed'],
                        'status' => 'Processing',
                        'remarks' => $request['remarks'],
                        'message' =>
                            'Your request has been received and is currently being processed.'
                    ]);

                    break;



                case 'For Released':

                    $subject = "Your Requested Document is Ready for Release";

                    $csmLink = base_url('csm/' . $request['reference_no']);
                    $csmtagalog_Link = base_url('csmtagalog/' . $request['reference_no']);

                    $message = view('emails/hr_status_update', [
                        'fullname' => $request['fullname'],
                        'reference_no' => $request['reference_no'],
                        'service' => $request['service_availed'],
                        'status' => 'For Released',
                        'remarks' => $request['remarks'],

                        'message' =>
                            'Your requested document is now ready for release. Please coordinate with the Human Resource Unit for claiming.',

                        'csm_link' => $csmLink,
                        'csmtagalog_Link' => $csmtagalog_Link
                    ]);

                    break;



                case 'Completed':

                    $subject = "Your HR Request Has Been Completed";

                    $message = view('emails/hr_status_update', [
                        'fullname' => $request['fullname'],
                        'reference_no' => $request['reference_no'],
                        'service' => $request['service_availed'],
                        'status' => 'Completed',
                        'remarks' => $request['remarks'],
                        'message' =>
                            'Your HR request has been successfully completed. Thank you for using the DOLE MIMAROPA e-Client Portal.'
                    ]);

                    break;



                case 'Rejected':

                    $subject = "Update Regarding Your HR Request";

                    $message = view('emails/hr_status_update', [
                        'fullname' => $request['fullname'],
                        'reference_no' => $request['reference_no'],
                        'service' => $request['service_availed'],
                        'status' => 'Rejected',
                        'remarks' => $request['remarks'],
                        'message' =>
                            'We regret to inform you that your request cannot be processed at this time.'
                    ]);

                    break;


                default:

                    $subject = "HR Request Status Update";

                    $message = view('emails/hr_status_update', [
                        'fullname' => $request['fullname'],
                        'reference_no' => $request['reference_no'],
                        'service' => $request['service_availed'],
                        'status' => $status,
                        'remarks' => $request['remarks'],
                        'message' =>
                            'Your request status has been updated.'
                    ]);

                    break;
            }


            $mailer->send(
                $request['email_address'],
                $subject,
                $message
            );

        }


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

    public function submitCSM()
    {
        $csmModel = new ClientSatisfactionModel();

        $data = [
            'reference_no' => $this->request->getPost('reference_no'),
            'office_name' => $this->request->getPost('office_name'),
            'transaction_no' => $this->request->getPost('transaction_no'),
            'service_availed' => $this->request->getPost('service_availed'),
            'client_type' => $this->request->getPost('client_type'),
            'date' => $this->request->getPost('date'),
            'sex' => $this->request->getPost('sex'),
            'age' => $this->request->getPost('age'),
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'cc1' => $this->request->getPost('cc1'),
            'cc2' => $this->request->getPost('cc2'),
            'cc3' => $this->request->getPost('cc3'),
            'sqd0' => $this->request->getPost('sqd0'),
            'sqd1' => $this->request->getPost('sqd1'),
            'sqd2' => $this->request->getPost('sqd2'),
            'sqd3' => $this->request->getPost('sqd3'),
            'sqd4' => $this->request->getPost('sqd4'),
            'sqd5' => $this->request->getPost('sqd5'),
            'sqd6' => $this->request->getPost('sqd6'),
            'sqd7' => $this->request->getPost('sqd7'),
            'sqd8' => $this->request->getPost('sqd8'),
            'suggestions' => $this->request->getPost('suggestions'),
            'email' => $this->request->getPost('email'),
        ];

        $result = $csmModel->insert($data);

        if (!$result) {
            dd(
                $csmModel->errors(),
                $csmModel->db->error(),
                $data
            );
        }

        return redirect()->back()->with(
            'success',
            'Thank you for answering our Feedback Survey. Your response has been recorded successfully.'
        );
    }

    public function csm($reference_no)
    {
        $requestModel = new HrRequestModel();

        $request = $requestModel
            ->where('reference_no', $reference_no)
            ->first();

        if (!$request) {
            return redirect()->back()
                ->with('error', 'Reference number not found');
        }

        return view('feedback/feedback_form_english', [
            'reference_no' => $request['reference_no'],
            'service_availed' => $request['service_availed'],
            'request' => $request,
        ]);
    }
    public function csmtagalog($reference_no)
    {
        $requestModel = new HrRequestModel();

        $request = $requestModel
            ->where('reference_no', $reference_no)
            ->first();

        if (!$request) {
            return redirect()->back()
                ->with('error', 'Reference number not found');
        }

        return view('feedback/feedback_form_tagalog', [
            'reference_no' => $request['reference_no'],
            'service_availed' => $request['service_availed'],
            'request' => $request,
        ]);
    }

    public function startProcessing()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $requestModel = new HrRequestModel();

        $data = $this->request->getJSON(true);

        $request = $requestModel->find($data['id']);

        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request not found.'
            ]);
        }

        if ($request['status'] != 'New Application') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This request has already been processed.'
            ]);
        }

        $requestModel->update($request['id'], [
            'status' => 'Processing',
            'date_processing' => date('Y-m-d H:i:s')
        ]);

        // Send Processing Email
        $mailer = new Mailer();

        $subject = "Your HR Request is Now Being Processed";

        $message = view('emails/hr_status_update', [
            'fullname' => $request['fullname'],
            'reference_no' => $request['reference_no'],
            'service' => $request['service_availed'],
            'status' => 'Processing',
            'remarks' => '',
            'message' => 'Your request has been received and is currently being processed.'
        ]);

        $mailer->send(
            $request['email_address'],
            $subject,
            $message
        );

        return $this->response->setJSON([
            'success' => true
        ]);
    }



}
