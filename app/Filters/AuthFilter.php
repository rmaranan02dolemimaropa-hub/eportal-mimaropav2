<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
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
            return redirect()->to(site_url('login'));
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE CHECK
        |--------------------------------------------------------------------------
        */

        if (!empty($arguments)) {

            $role = $session->get('role');

            if (!in_array($role, $arguments)) {
                return redirect()->to(
                    site_url('unauthorized')
                );
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