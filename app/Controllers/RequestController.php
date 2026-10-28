<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HrRequestModel;
use App\Models\HrEmploymentHistoryModel;
use App\Libraries\Mailer;
use App\Libraries\ActivityLogger;

class RequestController extends BaseController
{
    /** Service list (key from the form => label saved in the DB) */
    private const SERVICES = [
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

    //===================================================
    // LIST REQUESTS
    //===================================================

    public function index()
    {
        $response = service('response');
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');

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
                ->like('hr_requests.reference_no', $search)
                ->orLike('hr_requests.fullname', $search)
                ->orLike('hr_requests.email_address', $search)
                ->orLike('hr_requests.contact_number', $search)
                ->orLike('hr_requests.service_availed', $search)
                ->orLike('hr_requests.status', $search)
                ->groupEnd();
        }

        // Filters
        if ($year = $this->request->getGet('year')) {
            $query->like('hr_requests.date_requested', $year, 'after');
        }

        if ($status = $this->request->getGet('status')) {
            $query->where('hr_requests.status', $status);
        }

        if ($service = $this->request->getGet('service_availed')) {
            $query->where('hr_requests.service_availed', $service);
        }

        $requests = $query
            ->orderBy("
CASE
    WHEN hr_requests.status = 'New Application' THEN 1
    WHEN hr_requests.status = 'Processing' THEN 2
    WHEN hr_requests.status = 'For Releasing' THEN 3
    WHEN hr_requests.status = 'Completed' THEN 4
    WHEN hr_requests.status = 'Rejected' THEN 5
    ELSE 6
END
", '', false)
            ->orderBy('hr_requests.updated_at', 'DESC')
            ->orderBy('hr_requests.created_at', 'DESC')
            ->paginate(10);

        foreach ($requests as &$row) {
            $row['employment_history'] = $historyModel
                ->where('reference_no', $row['reference_no'])
                ->findAll();
        }
        unset($row);

        return view('dashboard/request', [
            'requests' => $requests,
            'pager' => $requestModel->pager
        ]);
    }

    //===================================================
    // SAVE REQUEST (admin side)
    //===================================================

    public function save()
    {
        $requestModel = new HrRequestModel();

        // Get selected service key
        $serviceKey = $this->request->getPost('service_availed');

        // Convert service7 → IPCR
        $serviceAvailed = self::SERVICES[$serviceKey] ?? $serviceKey;

        $data = $this->request->getPost([
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

        // Use the converted service name
        $data['service_availed'] = $serviceAvailed;

        $data['reference_no'] = 'HR-' . date('YmdHis');
        $data['status'] = 'New Application';

        $certification = $this->request->getPost('certification') ?? [];

        $data['certification'] = is_array($certification)
            ? implode(', ', $certification)
            : $certification;

        if ($requestModel->insert($data)) {
            ActivityLogger::record(
                'REQUEST_SUBMITTED',
                'REQUESTS',
                'Manual entry: ' . $data['reference_no'] . ' - ' . $serviceAvailed . ' (' . ($data['fullname'] ?? '') . ')'
            );
        }

        return redirect()
            ->to(site_url('request'))
            ->with('success', 'HR request submitted successfully.');
    }

    //===================================================
    // UPDATE REQUEST
    //===================================================

    public function update($id)
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
            'cancellation_reason' => $this->request->getPost('cancellation_reason'),
        ];

        // Update status date automatically
        switch ($status) {
            case 'Processing':
                $data['date_processing'] = date('Y-m-d H:i:s');
                break;

            case 'For Releasing':
                $data['date_released'] = date('Y-m-d H:i:s');
                break;

            case 'Completed':
                $data['date_completed'] = date('Y-m-d H:i:s');
                break;

            case 'Rejected':
                $data['date_rejected'] = date('Y-m-d H:i:s');
                break;

            case 'Canceled':
                $data['date_canceled'] = date('Y-m-d H:i:s');
                break;
        }

        // Old status, for the activity log
        $before = $requestModel->find($id);

        $requestModel->update($id, $data);

        $request = $requestModel->find($id);

        //===================================================
        // ACTIVITY LOG
        //===================================================

        if ($request) {
            $oldStatus = $before['status'] ?? '';
            $label = $request['reference_no'] . ' - ' . $request['service_availed'] . ' (' . $request['fullname'] . ')';

            if ($status !== $oldStatus) {
                $actionMap = [
                    'Processing' => 'REQUEST_PROCESSING',
                    'For Releasing' => 'REQUEST_FOR_RELEASING',
                    'Completed' => 'REQUEST_COMPLETED',
                    'Rejected' => 'REQUEST_REJECTED',
                    'Canceled' => 'REQUEST_CANCELED',
                ];

                ActivityLogger::record(
                    $actionMap[$status] ?? 'REQUEST_UPDATED',
                    'REQUESTS',
                    $label . ': ' . ($oldStatus ?: '?') . ' → ' . $status
                );
            } else {
                // Same status: only the details/remarks were edited
                ActivityLogger::record('REQUEST_UPDATED', 'REQUESTS', 'Edited details: ' . $label);
            }
        }

        //===================================================
        // SEND STATUS UPDATE EMAIL TO CLIENT
        //===================================================

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

                case 'For Releasing':

                    $subject = "Your Requested Document is Ready for Release";

                    $csmLink = base_url('csm/' . $request['reference_no']);
                    $csmtagalog_Link = base_url('csmtagalog/' . $request['reference_no']);
                    $feedbackHr_Link = base_url('feedback/hr/' . $request['reference_no']);

                    $message = view('emails/hr_status_update', [
                        'fullname' => $request['fullname'],
                        'reference_no' => $request['reference_no'],
                        'service' => $request['service_availed'],
                        'status' => 'For Releasing',
                        'remarks' => $request['remarks'],
                        'message' =>
                            'Your requested document is now ready for release. Please coordinate with the Human Resource Unit for claiming.',
                        'csm_link' => $csmLink,
                        'csmtagalog_Link' => $csmtagalog_Link,
                        'feedbackHr_link' => $feedbackHr_Link
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

    //===================================================
    // CANCEL REQUEST
    //===================================================

    public function cancel($id)
    {
        $requestModel = new HrRequestModel();

        $request = $requestModel->find($id);

        if (!$request) {
            return redirect()
                ->to(site_url('request'))
                ->with('error', 'Request not found.');
        }

        $cancellationReason = $this->request->getPost('cancellation_reason');

        $requestModel->update($id, [
            'status' => 'Canceled', // capital C para consistent sa buong system
            'date_canceled' => date('Y-m-d H:i:s'),
            'cancellation_reason' => $cancellationReason,
        ]);

        //===================================================
        // ACTIVITY LOG
        //===================================================

        ActivityLogger::record(
            'REQUEST_CANCELED',
            'REQUESTS',
            $request['reference_no'] . ' - ' . $request['service_availed'] . ' (' . $request['fullname'] . '): '
            . $request['status'] . ' → Canceled'
            . ($cancellationReason ? ' | Reason: ' . $cancellationReason : '')
        );

        //===================================================
        // SEND CANCELLATION EMAIL TO CLIENT
        //===================================================

        if (!empty($request['email_address'])) {

            $mailer = new Mailer();

            $subject = "Your HR Request Has Been Canceled";

            $message = view('emails/hr_status_update', [
                'fullname' => $request['fullname'],
                'reference_no' => $request['reference_no'],
                'service' => $request['service_availed'],
                'status' => 'Canceled',
                'remarks' => $cancellationReason,
                'message' =>
                    'Your HR request has been canceled. If you believe this was a mistake or you wish to submit a new request, please contact the Human Resource Unit.'
            ]);

            $mailer->send(
                $request['email_address'],
                $subject,
                $message
            );
        }

        return redirect()
            ->to(site_url('request'))
            ->with('success', 'HR request canceled successfully.');
    }

    //===================================================
    // START PROCESSING (AJAX)
    //===================================================

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

        ActivityLogger::record(
            'REQUEST_PROCESSING',
            'REQUESTS',
            'Started processing ' . $request['reference_no'] . ' - ' . $request['service_availed'] . ' (' . $request['fullname'] . ')'
        );

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

    //===================================================
    // REQUEST HISTORY
    //===================================================

    public function history($id)
    {
        $requestModel = new HrRequestModel();

        // Make sure user is still logged in
        if (!session()->get('logged_in')) {
            return redirect()->to(site_url('login'));
        }

        $request = $requestModel->find($id);

        if (!$request) {
            return redirect()
                ->to(site_url('dashboard'))
                ->with('error', 'Request not found.');
        }

        return view('request/history', [
            'title' => 'Request History',
            'request' => $request
        ]);
    }
}




