<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * While session 'pw_change_reason' is set (new account, just finished MFA set-up, or
 * password older than 3 months) every page except the change-password screen and
 * logout sends the user back to it, so it can't be skipped by typing another URL.
 */
class PasswordChangeFilter implements FilterInterface
{
    private const ALLOWED = ['password/change', 'password/update', 'logout'];

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in') || !$session->get('pw_change_reason')) {
            return;
        }

        if (in_array(trim(uri_string(), '/'), self::ALLOWED, true)) {
            return;
        }

        // Fetch / AJAX calls (navbar search, notifications...) get a clear 403 instead of a redirect
        if ($request->isAJAX()) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['error' => 'Password change required.']);
        }

        return redirect()->to(base_url('password/change'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}