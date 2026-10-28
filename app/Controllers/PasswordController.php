<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Libraries\MfaService;

/**
 * Change password: forced after MFA set-up and every 3 months, but also usable on demand.
 *
 *   GET  /password/change
 *   POST /password/update
 */
class PasswordController extends BaseController
{
    public function change()
    {
        return view('password/change', [
            // 'first' | 'expired' | 'voluntary'
            'reason' => session()->get('pw_change_reason') ?: 'voluntary',
        ]);
    }

    public function update()
    {
        $id = (int) session()->get('user_id');

        // 5 attempts per 5 minutes, same as the MFA pages
        if (!service('throttler')->check('pw-change-' . $id, 5, 300)) {
            return redirect()->back()->with('error', 'Too many attempts. Wait a few minutes and try again.');
        }

        $rules = [
            'current_password' => 'required',
            // bcrypt only reads the first 72 bytes, so cap the length
            'new_password' => 'required|min_length[8]|max_length[72]|regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]',
            'confirm_password' => 'required|matches[new_password]',
        ];
        $messages = [
            'current_password' => ['required' => 'Enter your current password.'],
            'new_password' => [
                'required' => 'Enter a new password.',
                'min_length' => 'The new password must be at least 8 characters.',
                'max_length' => 'The new password is too long (72 characters at most).',
                'regex_match' => 'The new password needs at least one letter and one number.',
            ],
            'confirm_password' => [
                'required' => 'Confirm your new password.',
                'matches' => 'The two new passwords do not match.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $db = db_connect();
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();
        $current = (string) $this->request->getPost('current_password');
        $new = (string) $this->request->getPost('new_password');

        if (!$user || !password_verify($current, $user['password'])) {
            ActivityLogger::record('PASSWORD_CHANGE_FAILED', 'AUTH', 'Wrong current password');
            return redirect()->back()->with('error', 'Your current password is not correct.');
        }

        if (password_verify($new, $user['password'])) {
            return redirect()->back()->with('error', 'The new password must be different from your current one.');
        }

        $db->table('users')->where('id', $id)->update([
            'password' => password_hash($new, PASSWORD_DEFAULT),
            'password_changed_at' => date('Y-m-d H:i:s'),
            'must_change_password' => 0,
        ]);

        ActivityLogger::record('PASSWORD_CHANGED', 'AUTH', 'Changed password');

        session()->remove('pw_change_reason');

        return redirect()->to((new MfaService())->landingUrl($user['role']))
            ->with('success', 'Your password was updated.');
    }
}