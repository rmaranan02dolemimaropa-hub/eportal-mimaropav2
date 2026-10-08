<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Shortcut keywords for route filters, e.g. 'auth:admin'.
     * A literal role name (e.g. 'auth:SUPERIOR') also works.
     */
    private const GROUPS = [
        'admin' => ['IT ADMINISTRATOR', 'HR ADMINISTRATOR'],
        'it' => ['IT ADMINISTRATOR'],
        'hr' => ['HR ADMINISTRATOR'],
        'superior' => ['SUPERIOR'],
    ];

    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        $session = session();

        /*
        |--------------------------------------------------------------------------
        | USER MUST BE LOGGED IN
        |--------------------------------------------------------------------------
        */

        if (!$session->get('logged_in')) {
            if ($request->isAJAX()) {
                return service('response')->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.',
                ]);
            }
            return redirect()->to(site_url('login'));
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE CHECK (only when the route passes arguments)
        |--------------------------------------------------------------------------
        */

        if (!empty($arguments)) {

            $role = strtoupper(trim((string) $session->get('role')));

            $allowed = [];
            foreach ((array) $arguments as $arg) {
                $key = strtolower(trim((string) $arg));
                $allowed = array_merge(
                    $allowed,
                    self::GROUPS[$key] ?? [strtoupper(trim((string) $arg))]
                );
            }

            if (!in_array($role, $allowed, true)) {

                if ($request->isAJAX()) {
                    return service('response')->setStatusCode(403)->setJSON([
                        'success' => false,
                        'message' => 'You do not have access to this action.',
                    ]);
                }

                // A SUPERIOR only has the TIA page, so send them back there
                if ($role === 'SUPERIOR') {
                    return redirect()->to(site_url('tia_page'))
                        ->with('error', 'You do not have access to that page.');
                }

                return redirect()->to(site_url('unauthorized'));
            }
        }

        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        /*
        |--------------------------------------------------------------------------
        | PREVENT CACHING OF AUTHENTICATED PAGES
        |--------------------------------------------------------------------------
        */

        $response->setHeader(
            'Cache-Control',
            'no-store, no-cache, must-revalidate, max-age=0'
        );

        $response->setHeader(
            'Pragma',
            'no-cache'
        );

        $response->setHeader(
            'Expires',
            '0'
        );
    }
}