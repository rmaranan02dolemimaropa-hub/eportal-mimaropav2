<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\SuperiorModel;

class EmployeeController extends BaseController
{
    protected EmployeeModel $model;
    protected SuperiorModel $superiors;

    public function __construct()
    {
        $this->model = new EmployeeModel();
        $this->superiors = new SuperiorModel();
    }

    /** Employees joined with their superior's details */
    private function employeesWithSuperior()
    {
        return $this->model
            ->select('employees.*, s.name AS superior_name, s.employee_code AS superior_code, s.email AS superior_email')
            ->join('superiors s', 's.id = employees.superior_id', 'left')
            ->orderBy('employees.name', 'ASC');
    }

    public function index()
    {
        return view('dashboard/dole-employees', [
            'employees' => $this->employeesWithSuperior()->findAll(),
            'superiors' => $this->superiors->orderBy('name', 'ASC')->findAll(),
            'offices' => EmployeeModel::OFFICES,

        ]);
    }

    public function store()
    {
        $data = $this->payload();


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
        $data['id'] = $id;

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
        $rows = $this->employeesWithSuperior()->findAll();

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads it correctly
        fputcsv($out, [
            'EMPLOYEE CODE',
            'NAME',
            'EMAIL',
            'POSITION',
            'OFFICE',
            'IMMEDIATE SUPERIOR CODE',
            'IMMEDIATE SUPERIOR',
            'IMMEDIATE SUPERIOR EMAIL',
        ]);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['employee_code'],
                $r['name'],
                $r['email'],
                $r['position'],
                $r['office'] ?? '',
                $r['superior_code'],
                $r['superior_name'],
                $r['superior_email'],
            ]);
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
        $csv = "\xEF\xBB\xBF" . "EMPLOYEE CODE,NAME,EMAIL,POSITION,OFFICE,IMMEDIATE SUPERIOR\r\n"
            . "EMP-1001,Juan Dela Cruz,juan@example.com,Clerk I,DOLE MIMAROPA REGIONAL OFFICE,John Doe\r\n";

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
        $officeIdx = array_search('OFFICE', $header); // optional

        $superiorCols = [
            'code' => array_search('IMMEDIATE SUPERIOR CODE', $header),
            'name' => array_search('IMMEDIATE SUPERIOR', $header),
            'email' => array_search('IMMEDIATE SUPERIOR EMAIL', $header),
        ];
        $hasSuperiorCols = $superiorCols['code'] !== false || $superiorCols['name'] !== false;

        // EMPLOYEE CODE, NAME, EMAIL, POSITION are required; OFFICE and the superior columns are optional
        if (
            $map['employee_code'] === false || $map['name'] === false
            || $map['email'] === false || $map['position'] === false
        ) {
            fclose($handle);
            return redirect()->to('/employees')
                ->with('error', 'Invalid header. Required columns: EMPLOYEE CODE, NAME, EMAIL, POSITION.');
        }

        $inserted = $updated = $skipped = 0;
        $superiorCache = [];
        $superiorNames = []; // id => name
        $db = db_connect();
        $db->transStart();

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue; // blank line
            }

            $data = [];
            foreach ($map as $field => $idx) {
                $data[$field] = trim($row[$idx] ?? '');
            }

            // No code -> skip (codes are no longer auto-generated)
            if ($data['employee_code'] === '') {
                $skipped++;
                continue;
            }

            // Office: only touched when the CSV has an OFFICE column
            if ($officeIdx !== false) {
                $data['office'] = EmployeeModel::normalizeOffice($row[$officeIdx] ?? '');
            }

            // Superior: only touched when the CSV has a superior column,
            // so re-importing an older CSV won't blank out existing superiors
            if ($hasSuperiorCols) {
                $sCode = $superiorCols['code'] !== false ? trim($row[$superiorCols['code']] ?? '') : '';
                $sName = $superiorCols['name'] !== false ? trim($row[$superiorCols['name']] ?? '') : '';
                $sEmail = $superiorCols['email'] !== false ? trim($row[$superiorCols['email']] ?? '') : '';

                $sid = $this->resolveSuperior($sCode, $sName, $sEmail, $superiorCache);

                if ($sid && !isset($superiorNames[$sid])) {
                    $superiorNames[$sid] = $this->superiors->find($sid)['name'] ?? null;
                }

                $data['superior_id'] = $sid;
                $data['immediate_superior'] = $sid ? $superiorNames[$sid] : null;
            }

            $existing = $this->model->where('employee_code', $data['employee_code'])->first();

            if ($existing) {
                $data['id'] = $existing['id']; // lets is_unique[...,id,{id}] ignore this record itself
                $this->model->update($existing['id'], $data) ? $updated++ : $skipped++;
            } else {
                $this->model->insert($data) ? $inserted++ : $skipped++;
            }
        }
        fclose($handle);
        $db->transComplete();

        return redirect()->to('/employees')
            ->with('success', "Import done: {$inserted} added, {$updated} updated, {$skipped} skipped.");
    }
    /**
     * Find a superior by code, then by name; create one if not found.
     * Returns the superior id, or null when the CSV cell is empty.
     */
    private function resolveSuperior(string $code, string $name, string $email, array &$cache): ?int
    {
        if ($code === '' && $name === '') {
            return null;
        }

        $key = strtolower($code . '|' . $name);
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $found = null;
        if ($code !== '') {
            $found = $this->superiors->where('employee_code', $code)->first();
        }
        if (!$found && $name !== '') {
            $found = $this->superiors->where('LOWER(name)', strtolower($name))->first();
        }

        if ($found) {
            // fill in a missing email
            if ($email !== '' && empty($found['email'])) {
                $this->superiors->update($found['id'], ['email' => $email]);
            }
            return $cache[$key] = (int) $found['id'];
        }

        $newId = $this->superiors->insert([
            'name' => $name !== '' ? $name : $code,
            'employee_code' => $code !== '' ? $code : null,
            'email' => $email !== '' ? $email : null,
        ]);

        return $cache[$key] = $newId ? (int) $newId : null;
    }

    private function payload(): array
    {
        $sid = (int) $this->request->getPost('superior_id');
        $name = null;
        if ($sid > 0) {
            $s = $this->superiors->find($sid);
            $name = $s['name'] ?? null;
        }
        $office = trim((string) $this->request->getPost('office'));

        return [
            'employee_code' => trim((string) $this->request->getPost('employee_code')),
            'name' => trim((string) $this->request->getPost('name')),
            'email' => trim((string) $this->request->getPost('email')),
            'position' => trim((string) $this->request->getPost('position')),
            'office' => EmployeeModel::normalizeOffice($this->request->getPost('office')),
            'superior_id' => $sid > 0 ? $sid : null,
            'immediate_superior' => $name,
        ];
    }

    public function all()
    {
        return $this->response->setJSON(
            $this->model
                ->select('employees.id, employees.employee_code, employees.name, employees.email, employees.position, employees.office, employees.division, employees.superior_id, s.name AS superior_name')
                ->join('superiors s', 's.id = employees.superior_id', 'left')
                ->orderBy('employees.name', 'ASC')
                ->findAll()
        );
    }
    /** Public: used by the TIA form to autofill Office / Immediate Superior by email */
    public function lookup()
    {
        $email = strtolower(trim((string) $this->request->getGet('email')));

        $row = $email === '' ? null : $this->model
            ->select('employees.name, employees.position, employees.office, s.name AS superior_name')
            ->join('superiors s', 's.id = employees.superior_id', 'left')
            ->where('LOWER(employees.email)', $email)
            ->first();

        return $this->response->setJSON(['found' => (bool) $row, 'data' => $row]);
    }
}