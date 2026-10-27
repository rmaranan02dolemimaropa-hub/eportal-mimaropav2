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
// DEACTIVATE USER
//===================================================

    public function deactivate($id)
    {
        $id = (int) $id;

        // Prevent locking yourself out
        if ($id === (int) session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user = $this->model->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $this->model->update($id, ['status' => 'INACTIVE']);

        return redirect()->back()->with('success', $user['fullname'] . ' has been deactivated.');
    }

    //===================================================
// ACTIVATE USER
//===================================================

    public function activate($id)
    {
        $id = (int) $id;

        $user = $this->model->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $this->model->update($id, ['status' => 'ACTIVE']);

        return redirect()->back()->with('success', $user['fullname'] . ' has been activated.');
    }

    //===================================================
    // RESET MFA (lost phone / lost passkey)
    //===================================================

    public function resetMfa($id)
    {
        $id = (int) $id;

        $user = $this->model->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Wipes authenticator secret, backup codes and passkeys.
        // The user must enrol again at their next login.
        (new \App\Libraries\MfaService())->resetUser($id);

        return redirect()->back()->with('success', 'Two-step verification was reset for ' . $user['fullname'] . '. They must set it up again at next login.');
    }
}
