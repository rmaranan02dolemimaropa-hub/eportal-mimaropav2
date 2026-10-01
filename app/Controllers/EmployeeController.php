<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EmployeeModel;

class EmployeeController extends BaseController
{

    protected EmployeeModel $model;

    public function __construct()
    {
        $this->model = new EmployeeModel();
    }
    public function index()
    {
        return view('dashboard/dole-employees', [
            'employees' => $this->model->orderBy('name', 'ASC')->findAll(),
            'nextCode' => $this->model->generateCode(),
        ]);
    }

    public function store()
    {
        $data = $this->payload();
        $data['employee_code'] = $this->model->generateCode(); // auto-generated

        if (!$this->model->insert($data)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->model->errors()));
        }
        return redirect()->to('/employees')->with('success', 'Employee added.');
    }

    public function update($id)
    {
        $id = (int) $id;
        $existing = $this->model->find($id);
        if (!$existing) {
            return redirect()->to('/employees')->with('error', 'Employee not found.');
        }

        $data = $this->payload();
        $data['employee_code'] = $existing['employee_code']; // code never changes

        if (!$this->model->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->model->errors()));
        }
        return redirect()->to('/employees')->with('success', 'Employee updated.');
    }

    public function delete($id)
    {
        $this->model->delete((int) $id);
        return redirect()->to('/employees')->with('success', 'Employee deleted.');
    }

    public function export()
    {
        $rows = $this->model->orderBy('name', 'ASC')->findAll();

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads it correctly
        fputcsv($out, ['EMPLOYEE CODE', 'NAME', 'EMAIL', 'POSITION']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['employee_code'], $r['name'], $r['email'], $r['position']]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="employees_' . date('Ymd_His') . '.csv"')
            ->setBody($csv);
    }

    public function template()
    {
        $csv = "\xEF\xBB\xBF" . "NAME,EMAIL,POSITION\r\n"
            . "Juan Dela Cruz,juan@example.com,Clerk I\r\n";

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="employees_template.csv"')
            ->setBody($csv);
    }

    public function import()
    {
        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid() || strtolower($file->getClientExtension()) !== 'csv') {
            return redirect()->to('/employees')->with('error', 'Please upload a valid .csv file.');
        }

        $handle = fopen($file->getTempName(), 'r');
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return redirect()->to('/employees')->with('error', 'CSV file is empty.');
        }

        // strip BOM, normalize header names
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        $header = array_map(fn($h) => strtoupper(trim($h)), $header);

        $map = [
            'employee_code' => array_search('EMPLOYEE CODE', $header),
            'name' => array_search('NAME', $header),
            'email' => array_search('EMAIL', $header),
            'position' => array_search('POSITION', $header),
        ];
        // EMPLOYEE CODE column is optional; NAME, EMAIL, POSITION are required
        if ($map['name'] === false || $map['email'] === false || $map['position'] === false) {
            fclose($handle);
            return redirect()->to('/employees')
                ->with('error', 'Invalid header. Required columns: NAME, EMAIL, POSITION (EMPLOYEE CODE optional).');
        }

        $inserted = $updated = $skipped = 0;
        $db = db_connect();
        $db->transStart();

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue; // blank line
            }
            $data = [];
            foreach ($map as $field => $idx) {
                $data[$field] = ($idx === false) ? '' : trim($row[$idx] ?? '');
            }

            $existing = $data['employee_code'] !== ''
                ? $this->model->where('employee_code', $data['employee_code'])->first()
                : null;

            if ($existing) {
                $this->model->update($existing['id'], $data) ? $updated++ : $skipped++;
            } else {
                if ($data['employee_code'] === '') {
                    $data['employee_code'] = $this->model->generateCode();
                }
                $this->model->insert($data) ? $inserted++ : $skipped++;
            }
        }
        fclose($handle);
        $db->transComplete();

        return redirect()->to('/employees')
            ->with('success', "Import done: {$inserted} added, {$updated} updated, {$skipped} skipped.");
    }

    private function payload(): array
    {
        return [
            'name' => trim((string) $this->request->getPost('name')),
            'email' => trim((string) $this->request->getPost('email')),
            'position' => trim((string) $this->request->getPost('position')),
        ];
    }
}
