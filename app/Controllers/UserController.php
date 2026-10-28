<?php

namespace App\Controllers;

use App\Libraries\MfaService;
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
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
            'must_change_password' => 1,       // temporary password set by an admin
            'password_changed_at' => null,
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

        if ($this->model->where('username', $username)->where('id !=', $id)->first()) {
            return redirect()->back()->withInput()->with('error', 'Username already exists.');
        }

        if ($this->model->where('email', $email)->where('id !=', $id)->first()) {
            return redirect()->back()->withInput()->with('error', 'Email address already exists.');
        }

        if (!in_array($role, self::ROLES, true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid role.');
        }

        if (!in_array($status, ['ACTIVE', 'INACTIVE'], true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid status.');
        }

        // Prevent locking yourself out through the edit form
        if ($id === (int) session()->get('user_id') && $status === 'INACTIVE') {
            return redirect()->back()->withInput()->with('error', 'You cannot deactivate your own account.');
        }

        $data = [
            'fullname' => trim((string) $this->request->getPost('fullname')),
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'status' => $status,
        ];

        $password = trim((string) $this->request->getPost('password'));
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            $data['must_change_password'] = 1;   // set by an admin, so it is temporary
            $data['password_changed_at'] = null;
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
    // RESET MFA (lost phone / lost authenticator)
    //===================================================

    public function resetMfa($id)
    {
        $id = (int) $id;

        $user = $this->model->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        try {
            // Clears the authenticator secret and backup codes; the user
            // must enrol again at their next login.
            (new MfaService())->resetUser($id);
        } catch (\Throwable $e) {
            log_message('error', 'MFA reset failed for user {id}: {msg}', ['id' => $id, 'msg' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Reset failed: ' . $e->getMessage());
        }

        return redirect()->back()->with(
            'success',
            'Two-step verification was reset for ' . $user['fullname'] . '. They must set it up again at next login.'
        );
    }
}