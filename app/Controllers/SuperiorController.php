<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SuperiorModel;

class SuperiorController extends BaseController
{
    protected SuperiorModel $model;

    public function __construct()
    {
        $this->model = new SuperiorModel();
    }

    /** List page */
    public function index()
    {
        return view('dashboard/dole-superiors', [
            'superiors' => $this->model->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    /** Add from the superiors page */
    public function store()
    {
        $data = $this->payload();

        if ($data['employee_code'] === null) {
            return redirect()->back()->withInput()->with('error', 'Employee code is required.');
        }

        if (!$this->model->insert($data)) {
            return redirect()->back()->withInput()
                ->with('error', implode(' ', $this->model->errors()));
        }
        return redirect()->to('/superiors')->with('success', 'Superior added.');
    }

    /** Edit */
    public function update($id)
    {
        $id = (int) $id;

        if (!$this->model->find($id)) {
            return redirect()->to('/superiors')->with('error', 'Superior not found.');
        }

        $data = $this->payload();
        $data['id'] = $id; // lets is_unique[...,id,{id}] ignore this record itself

        if ($data['employee_code'] === null) {
            return redirect()->back()->withInput()->with('error', 'Employee code is required.');
        }

        if (!$this->model->update($id, $data)) {
            return redirect()->back()->withInput()
                ->with('error', implode(' ', $this->model->errors()));
        }
        return redirect()->to('/superiors')->with('success', 'Superior updated.');
    }

    /** Delete (employees.superior_id becomes NULL via ON DELETE SET NULL) */
    public function delete($id)
    {
        $this->model->delete((int) $id);
        return redirect()->to('/superiors')->with('success', 'Superior deleted.');
    }

    /** AJAX: add from the Tom Select dropdown ("type a new name"); code is added later on the Superiors page */
    public function quickAdd()
    {
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));

        if ($name === '') {
            return $this->response->setStatusCode(422)
                ->setJSON(['error' => 'Name is required.', 'csrf' => csrf_hash()]);
        }

        // reuse if the name already exists (case-insensitive)
        $existing = $this->model->where('LOWER(name)', strtolower($name))->first();
        if ($existing) {
            return $this->response->setJSON([
                'id' => $existing['id'],
                'name' => $existing['name'],
                'employee_code' => $existing['employee_code'] ?? '',
                'email' => $existing['email'] ?? '',
                'csrf' => csrf_hash(),
            ]);
        }

        $id = $this->model->insert([
            'name' => $name,
            'employee_code' => null,
            'email' => $email !== '' ? $email : null,
        ]);

        if (!$id) {
            return $this->response->setStatusCode(422)->setJSON([
                'error' => implode(' ', $this->model->errors()),
                'csrf' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'id' => $id,
            'name' => $name,
            'employee_code' => '',
            'email' => $email,
            'csrf' => csrf_hash(),
        ]);
    }

    /** AJAX: JSON list (for server-side search / refreshing dropdowns) */
    public function all()
    {
        $q = trim((string) $this->request->getGet('q'));

        $rows = $q !== ''
            ? $this->model->search($q, 50) // helper from SuperiorModel
            : $this->model->orderBy('name', 'ASC')->findAll();

        return $this->response->setJSON($rows);
    }

    /** Collects and cleans form input; empty optional fields become NULL */
    private function payload(): array
    {
        $code = trim((string) $this->request->getPost('employee_code'));
        $email = trim((string) $this->request->getPost('email'));
        $position = trim((string) $this->request->getPost('position'));

        return [
            'employee_code' => $code !== '' ? $code : null,
            'name' => trim((string) $this->request->getPost('name')),
            'email' => $email !== '' ? $email : null,
            'position' => $position !== '' ? $position : null,
        ];
    }

}