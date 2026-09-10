<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

$routes->get('login', 'EportalController::login');
$routes->post('login/login_process', 'EportalController::login_process');


/*
|--------------------------------------------------------------------------
| PUBLIC / CLIENT ROUTES
|--------------------------------------------------------------------------
*/

$routes->get('/', 'EportalController::index');

$routes->get('service-record', 'EportalController::service_record');
$routes->post('service-record/save', 'EportalController::save');

$routes->get('csm/(:any)', 'EportalController::csm/$1');
$routes->get('csmtagalog/(:any)', 'EportalController::csmtagalog/$1');

$routes->post('submit-csm', 'EportalController::submitCSM');

$routes->get('feedback/hr/(:segment)', 'EportalController::feedback_form/$1');
$routes->post('feedback/save', 'EportalController::save_feedback');
$routes->get('feedback/print/hr/(:segment)', 'EportalController::print_hr/$1');

$routes->get(
    'feedback/print/english/(:num)',
    'EportalController::printEnglish/$1'
);

$routes->get(
    'feedback/print/tagalog/(:num)',
    'EportalController::printTagalog/$1'
);


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

$routes->group('', ['filter' => 'auth'], function ($routes) {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    $routes->get('dashboard', 'EportalController::dashboard');
    $routes->get('manual', 'EportalController::manual');


    /*
    |--------------------------------------------------------------------------
    | REQUESTS
    |--------------------------------------------------------------------------
    */

    $routes->get('request', 'EportalController::request');

    $routes->post(
        'request/save',
        'EportalController::saveRequest'
    );

    $routes->post(
        'request/start-processing',
        'EportalController::startProcessing'
    );

    $routes->post(
        'request/update/(:num)',
        'EportalController::updateRequest/$1'
    );

    $routes->post('request/cancel/(:num)', 'EportalController::cancelRequest/$1');


    /*
    |--------------------------------------------------------------------------
    | REQUEST HISTORY
    |--------------------------------------------------------------------------
    */

    $routes->get(
        'request/history/(:num)',
        'EportalController::requestHistory/$1'
    );


    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    $routes->get(
        'reports',
        'EportalController::reports'
    );

    $routes->get(
        'reports/print',
        'ExportController::printHrRequestReport'
    );

    $routes->get(
        'export/hr-request',
        'ExportController::exportHrRequestReport'
    );

    $routes->get(
        'export/reports-pdf',
        'ExportController::exportReportsPdf'
    );

    $routes->get(
        'export/reports-csv',
        'ExportController::exportReportsCsv'
    );


    /*
    |--------------------------------------------------------------------------
    | FEEDBACK
    |--------------------------------------------------------------------------
    */

    $routes->get(
        'feedback',
        'EportalController::feedback'
    );


    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    $routes->get(
        'users',
        'EportalController::users'
    );

    $routes->post(
        'users/store',
        'EportalController::storeUser'
    );

    $routes->post(
        'users/update/(:num)',
        'EportalController::updateUser/$1'
    );

    $routes->post(
        'users/delete/(:num)',
        'EportalController::deleteUser/$1'
    );


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    $routes->get(
        'logout',
        'EportalController::logout'
    );
});
