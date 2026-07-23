<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class EportalController extends BaseController
{
    public function index()
    {
        return view('eportal/index');
    }
}
