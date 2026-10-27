<?php

namespace App\Libraries;

/**
 * Writes one row to activity_logs. It never throws: a logging problem
 * must not break the page the user is on.
 *
 *   ActivityLogger::record('LOGIN_FAILED', 'AUTH', 'Wrong password', $userRow);
 *
 * $actor is any array with id / username / fullname / role (a users row works).
 * Leave it out to use the currently logged-in user. Only those four keys are
 * read, so passwords and MFA secrets in a users row are never stored.
 */
class ActivityLogger
{
    public static function record(string $action, string $module, string $description = '', ?array $actor = null): void
    {
        try {
            if ($actor === null) {
                $s = session();
                $actor = [
                    'id' => $s->get('user_id'),
                    'username' => $s->get('username'),
                    'fullname' => $s->get('fullname'),
                    'role' => $s->get('role'),
                ];
            }

            $request = service('request');
            $ip = method_exists($request, 'getIPAddress') ? $request->getIPAddress() : null;
            $agent = method_exists($request, 'getUserAgent') ? (string) $request->getUserAgent() : null;

            db_connect()->table('activity_logs')->insert([
                'user_id' => (int) ($actor['id'] ?? 0) > 0 ? (int) $actor['id'] : null,
                'username' => self::cut($actor['username'] ?? null, 100),
                'fullname' => self::cut($actor['fullname'] ?? null, 150),
                'role' => self::cut($actor['role'] ?? null, 30),
                'module' => self::cut($module, 30) ?? 'SYSTEM',
                'action' => self::cut($action, 60) ?? 'UNKNOWN',
                'description' => self::cut($description, 500),
                'ip_address' => self::cut($ip, 45),
                'user_agent' => self::cut($agent, 255),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Activity log failed: ' . $e->getMessage());
        }
    }

    /** Trim, drop control characters (no line breaks in a log line) and cap the length. */
    private static function cut($value, int $max): ?string
    {
        $value = trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) $value));

        return $value === '' ? null : mb_substr($value, 0, $max);
    }
}