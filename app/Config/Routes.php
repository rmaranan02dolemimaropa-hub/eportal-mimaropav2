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
$routes->post('service-record/manualsave', 'EportalController::manualsave');

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

    $routes->get('exports/satisfaction-excel/(:any)', 'ExportController::exportSatisfactionExcel/$1');
    $routes->get('exports/satisfaction-excel', 'ExportController::exportSatisfactionExcel');
    $routes->get('client-satisfaction-report', 'ExportController::report');

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


    // Training Impact Assessment - Dashboard
    $routes->get('tia_page', 'HrController::tia_page');
    $routes->post('tia/store-training', 'HrController::storeTraining');
    $routes->post('tia/add-participant', 'HrController::addParticipant');
    $routes->get('tia/participants/(:any)', 'HrController::getParticipants/$1');
    $routes->post('tia/add-participants-bulk', 'HrController::addParticipantsBulk');
    $routes->get('tia/export-participants/(:any)', 'HrController::exportParticipants/$1');
    $routes->post('tia/update-participant/(:num)', 'HrController::updateParticipant/$1');
    $routes->post('tia/update-training/(:segment)', 'HrController::updateTraining/$1');
    $routes->post('tia/delete-training/(:segment)', 'HrController::deleteTraining/$1');
    $routes->post('tia/update-status/(:segment)', 'HrController::updateTrainingStatus/$1');
    $routes->post('tia/delete-participant/(:num)', 'HrController::deleteParticipant/$1');
    $routes->post('tia/send-form-emails', 'HrController::sendFormEmails');
    $routes->get('tia_submitted/(:segment)', 'HrController::submitted/$1');

    $routes->get('employees', 'EmployeeController::index');
    $routes->post('employees/store', 'EmployeeController::store');
    $routes->post('employees/update/(:num)', 'EmployeeController::update/$1');
    $routes->post('employees/delete/(:num)', 'EmployeeController::delete/$1');
    $routes->get('employees/export', 'EmployeeController::export');
    $routes->get('employees/template', 'EmployeeController::template');
    $routes->post('employees/import', 'EmployeeController::import');
    $routes->get('employees/all', 'EmployeeController::all');
    $routes->get('employees/lookup', 'EmployeeController::lookup');

    $routes->get('superiors', 'SuperiorController::index');
    $routes->post('superiors/store', 'SuperiorController::store');
    $routes->post('superiors/update/(:num)', 'SuperiorController::update/$1');
    $routes->post('superiors/delete/(:num)', 'SuperiorController::delete/$1');
    $routes->post('superiors/quick-add', 'SuperiorController::quickAdd');
    $routes->get('superiors/all', 'SuperiorController::all');








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


// Training Impact Assessment annex d
$routes->get('tia_ad', 'HrController::create');
$routes->post('tia_store', 'HrController::store');



