<?php

namespace App\Controllers;

use App\Libraries\MfaService;

/**
 * TOTP enrolment + verification (pending-login routes: setup, confirm, verify, verifyProcess, cancel).
 */
class MfaController extends BaseController
{
    private MfaService $mfa;

    public function __construct()
    {
        $this->mfa = new MfaService();
    }

    // ============ ENROLMENT (forced for users with no MFA) ============

    public function setup()
    {
        $user = $this->mfa->pendingUser();
        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'Your sign-in session expired. Please log in again.');
        }
        if ((int) $user['mfa_enabled'] === 1) {
            return redirect()->to(base_url('mfa/verify'));
        }

        $session = session();
        $secret = $session->get('mfa_new_secret');
        if (!$secret) {
            $secret = $this->mfa->newSecret();
            $session->set('mfa_new_secret', $secret);
        }

        $uri = $this->mfa->otpUri($secret, $user['email'] ?: $user['username']);

        return view('mfa/setup', [
            'qrSvg' => $this->mfa->qrSvg($uri),
            'secret' => trim(chunk_split($secret, 4, ' ')),
        ]);
    }

    public function confirm()
    {
        $user = $this->mfa->pendingUser();
        $secret = session()->get('mfa_new_secret');
        if (!$user || !$secret) {
            return redirect()->to(base_url('login'))->with('error', 'Your sign-in session expired. Please log in again.');
        }

        if (!$this->throttle('mfa-setup-' . $user['id'])) {
            return redirect()->back()->with('error', 'Too many attempts. Wait a few minutes and try again.');
        }

        $ts = $this->mfa->checkCode($secret, (string) $this->request->getPost('code'));
        if ($ts === null) {
            return redirect()->back()->with('error', 'That code is not valid. Check your authenticator app and try again.');
        }

        db_connect()->table('users')->where('id', $user['id'])->update([
            'mfa_secret' => $this->mfa->encrypt($secret),
            'mfa_enabled' => 1,
            'mfa_last_ts' => $ts,
            // The password used to get here may be a temporary one: make them choose their own now.
            'must_change_password' => 1,
        ]);

        $user['mfa_enabled'] = 1;
        $user['must_change_password'] = 1;
        $this->mfa->completeLogin($user);

        // landingUrl() sends them to password/change while the flag is set
        return redirect()->to($this->mfa->landingUrl($user['role']));
    }

    // ============ LOGIN CHALLENGE ============

    public function verify()
    {
        $user = $this->mfa->pendingUser();
        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'Your sign-in session expired. Please log in again.');
        }
        if ((int) $user['mfa_enabled'] !== 1) {
            return redirect()->to(base_url('mfa/setup'));
        }

        return view('mfa/verify');
    }

    public function verifyProcess()
    {
        $user = $this->mfa->pendingUser();
        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'Your sign-in session expired. Please log in again.');
        }

        if (!$this->throttle('mfa-verify-' . $user['id'])) {
            return redirect()->back()->with('error', 'Too many attempts. Wait a few minutes and try again.');
        }

        if (!$this->mfa->verifyUserTotp($user, (string) $this->request->getPost('code'))) {
            return redirect()->back()->with('error', 'Invalid or already-used code.');
        }

        $this->mfa->completeLogin($user);

        // Goes to password/change instead when the password is flagged or older than 3 months
        return redirect()->to($this->mfa->landingUrl($user['role']));
    }

    public function cancel()
    {
        $this->mfa->clearPending();
        return redirect()->to(base_url('login'));
    }

    /** 5 attempts per 5 minutes per key. */
    private function throttle(string $key): bool
    {
        return service('throttler')->check($key, 5, 300);
    }
}