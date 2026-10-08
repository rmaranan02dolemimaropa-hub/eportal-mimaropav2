<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected UserModel $model;

    /** Allowed roles (must match the ENUM in the users table) */
    private const ROLES = ['IT ADMINISTRATOR', 'HR ADMINISTRATOR', 'SUPERIOR'];

    public function __construct()
    {
        $this->model = new UserModel();
    }

    //===================================================
    // LIST USERS
    //===================================================

    public function index()
    {
        return view('dashboard/users', [
            'users' => $this->model->paginate(10),
            'pager' => $this->model->pager,
        ]);
    }

    //===================================================
    // ADD USER
    //===================================================

    public function store()
    {
        $rules = [
            'fullname' => 'required|min_length[3]',
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[' . implode(',', self::ROLES) . ']',
            'status' => 'required|in_list[ACTIVE,INACTIVE]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->insert([
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

    public function update($id)
    {
        $id = (int) $id;

        if (!$this->model->find($id)) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $username = trim((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));
        $role = strtoupper(trim((string) $this->request->getPost('role')));
        $status = $this->request->getPost('status');

        // Another user already uses this username?
        if ($this->model->where('username', $username)->where('id !=', $id)->first()) {
            return redirect()->back()->withInput()->with('error', 'Username already exists.');
        }

        // Another user already uses this email?
        if ($this->model->where('email', $email)->where('id !=', $id)->first()) {
            return redirect()->back()->withInput()->with('error', 'Email address already exists.');
        }

        if (!in_array($role, self::ROLES, true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid role.');
        }

        if (!in_array($status, ['ACTIVE', 'INACTIVE'], true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid status.');
        }

        $data = [
            'fullname' => trim((string) $this->request->getPost('fullname')),
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'status' => $status,
        ];

        // Password is optional: blank means keep the current one
        $password = trim((string) $this->request->getPost('password'));
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->model->update($id, $data);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    //===================================================
    // DELETE USER
    //===================================================

    public function delete($id)
    {
        $id = (int) $id;

        // Prevent locking yourself out
        if ($id === (int) session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $this->model->delete($id);

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}