<?php

namespace App\Libraries;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use CodeIgniter\HTTP\RedirectResponse;
use PragmaRX\Google2FA\Google2FA;

/**
 * Shared helper for TOTP, backup codes and the "finish login" step.
 *
 * Needs:  composer require pragmarx/google2fa bacon/bacon-qr-code
 * Needs:  encryption.key set in .env (the TOTP secret is stored encrypted)
 * Needs:  password_expiry.sql applied (users.password_changed_at, users.must_change_password)
 */
class MfaService
{
    public const ISSUER = 'DOLE MIMAROPA E-Portal';
    public const PENDING_TTL = 600;   // seconds a half-finished login stays valid
    public const BACKUP_COUNT = 8;
    public const PASSWORD_MAX_AGE = '-3 months';   // older than this = must change

    private Google2FA $g2fa;

    public function __construct()
    {
        $this->g2fa = new Google2FA();
    }

    // ------------------------------------------------------------------
    // Login flow
    // ------------------------------------------------------------------

    /**
     * Call this from Login::login_process() AFTER the password and the
     * ACTIVE status have been verified, instead of creating the session.
     */
    public function startChallenge(array $user): RedirectResponse
    {
        $session = session();
        $session->remove(['user_id', 'logged_in']);          // never keep a half-trusted full session
        $session->set([
            'mfa_user_id' => (int) $user['id'],
            'mfa_started' => time(),
        ]);

        return redirect()->to(base_url(((int) $user['mfa_enabled'] === 1) ? 'mfa/verify' : 'mfa/setup'));
    }

    /** The user in the middle of a login (password OK, MFA not yet), or null. */
    public function pendingUser(): ?array
    {
        $session = session();
        $id = (int) $session->get('mfa_user_id');
        $started = (int) $session->get('mfa_started');

        if ($id <= 0 || (time() - $started) > self::PENDING_TTL) {
            $this->clearPending();
            return null;
        }

        $user = db_connect()->table('users')->where('id', $id)->get()->getRowArray();
        if (!$user || $user['status'] !== 'ACTIVE') {
            $this->clearPending();
            return null;
        }

        return $user;
    }

    public function clearPending(): void
    {
        session()->remove(['mfa_user_id', 'mfa_started', 'mfa_new_secret']);
    }

    /**
     * Create the real, logged-in session.
     *
     * !! Keep these keys identical to what your existing Login controller sets !!
     */
    public function completeLogin(array $user): void
    {
        $session = session();
        $this->clearPending();
        $session->regenerate(true);                           // prevent session fixation
        $session->set([
            'user_id' => (int) $user['id'],
            'fullname' => $user['fullname'],
            'username' => $user['username'],
            'role' => $user['role'],
            'logged_in' => true,
        ]);

        // 'first' (new / just enrolled) or 'expired' (older than 3 months). The filter
        // keeps the user on the change-password page until this is cleared.
        $reason = $this->passwordChangeReason($user);
        if ($reason !== null) {
            $session->set('pw_change_reason', $reason);
        } else {
            $session->remove('pw_change_reason');
        }

        ActivityLogger::record('LOGIN_SUCCESS', 'AUTH', 'Signed in', $user);
    }

    /**
     * Why this user must pick a new password right now, or null if they don't.
     *   first   = flagged (new account, admin-set password, or just finished MFA set-up)
     *   expired = never set, or older than PASSWORD_MAX_AGE
     */
    public function passwordChangeReason(array $user): ?string
    {
        if ((int) ($user['must_change_password'] ?? 0) === 1) {
            return 'first';
        }

        $changed = $user['password_changed_at'] ?? null;
        if (empty($changed)) {
            return 'expired';
        }

        return strtotime($changed) <= strtotime(self::PASSWORD_MAX_AGE) ? 'expired' : null;
    }

    /** Where a freshly logged-in user goes. SUPERIOR cannot open the admin-only dashboard. */
    public function landingUrl(?string $role): string
    {
        if (session()->get('pw_change_reason')) {
            return base_url('password/change');
        }

        return base_url(strtoupper(trim((string) $role)) === 'SUPERIOR' ? 'tia-superior/monitoring' : 'dashboard');
    }

    // ------------------------------------------------------------------
    // TOTP
    // ------------------------------------------------------------------

    public function newSecret(): string
    {
        return $this->g2fa->generateSecretKey(32);
    }

    public function otpUri(string $secret, string $account): string
    {
        return $this->g2fa->getQRCodeUrl(self::ISSUER, $account, $secret);
    }

    public function qrSvg(string $uri): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle(220, 1), new SvgImageBackEnd()));
        return $writer->writeString($uri);
    }

    public function encrypt(string $plain): string
    {
        return base64_encode(service('encrypter')->encrypt($plain));
    }

    public function decrypt(string $cipher): string
    {
        return service('encrypter')->decrypt(base64_decode($cipher, true) ?: '');
    }

    /** Verify a code against a plain secret (used during enrolment). Returns the matched time-step or null. */
    public function checkCode(string $secret, string $code, int $lastTs = 0): ?int
    {
        $code = preg_replace('/\D/', '', $code);
        if (strlen($code) !== 6) {
            return null;
        }

        $ts = $this->g2fa->verifyKeyNewer($secret, $code, $lastTs, 1);   // ±30s window, no replay
        if ($ts === false) {
            return null;
        }

        return is_int($ts) ? $ts : $this->g2fa->getTimestamp();
    }

    /** Verify a login-time TOTP for a stored user. */
    public function verifyUserTotp(array $user, string $code): bool
    {
        if (empty($user['mfa_secret'])) {
            return false;
        }

        try {
            $secret = $this->decrypt($user['mfa_secret']);
        } catch (\Throwable $e) {
            log_message('error', 'MFA secret decrypt failed for user ' . $user['id']);
            return false;
        }

        $ts = $this->checkCode($secret, $code, (int) ($user['mfa_last_ts'] ?? 0));
        if ($ts === null) {
            return false;
        }

        db_connect()->table('users')->where('id', $user['id'])->update(['mfa_last_ts' => $ts]);
        return true;
    }

    // ------------------------------------------------------------------
    // Backup codes
    // ------------------------------------------------------------------

    /** Replace all backup codes. Returns the plain codes ONCE for display. */
    public function regenerateBackupCodes(int $userId): array
    {
        $db = db_connect();
        $db->table('user_backup_codes')->where('user_id', $userId)->delete();

        $plain = [];
        $now = date('Y-m-d H:i:s');
        for ($i = 0; $i < self::BACKUP_COUNT; $i++) {
            $raw = strtoupper(bin2hex(random_bytes(5)));          // 10 hex chars
            $plain[] = substr($raw, 0, 5) . '-' . substr($raw, 5);
            $db->table('user_backup_codes')->insert([
                'user_id' => $userId,
                'code_hash' => password_hash($raw, PASSWORD_DEFAULT),
                'created_at' => $now,
            ]);
        }

        return $plain;
    }

    public function consumeBackupCode(int $userId, string $input): bool
    {
        $norm = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $input));
        if (strlen($norm) !== 10) {
            return false;
        }

        $db = db_connect();
        $rows = $db->table('user_backup_codes')->where('user_id', $userId)->where('used_at', null)->get()->getResultArray();

        foreach ($rows as $row) {
            if (password_verify($norm, $row['code_hash'])) {
                $db->table('user_backup_codes')->where('id', $row['id'])->update(['used_at' => date('Y-m-d H:i:s')]);
                return true;
            }
        }

        return false;
    }

    public function remainingBackupCodes(int $userId): int
    {
        return db_connect()->table('user_backup_codes')->where('user_id', $userId)->where('used_at', null)->countAllResults();
    }

    // ------------------------------------------------------------------
    // Admin reset
    // ------------------------------------------------------------------

    /**
     * Wipe the second factor so the user must re-enrol at next login.
     *
     * Needs users.mfa_secret and users.mfa_last_ts to be NULLable.
     */
    /** Wipe the second factor so the user must re-enrol at next login. */
    public function resetUser(int $userId): void
    {
        $db = db_connect();

        $db->table('users')->where('id', $userId)->update([
            'mfa_secret' => null,
            'mfa_enabled' => 0,
            'mfa_last_ts' => null,
        ]);

        // Only if the backup-codes table actually exists in this database
        if ($db->tableExists('user_backup_codes')) {
            $db->table('user_backup_codes')->where('user_id', $userId)->delete();
        }
    }
}