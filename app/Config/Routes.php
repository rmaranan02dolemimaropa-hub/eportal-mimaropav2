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

// --- MFA (half-finished login: password OK, second step pending) ---
$routes->get('mfa/setup', 'MfaController::setup');
$routes->post('mfa/setup', 'MfaController::confirm');
$routes->get('mfa/verify', 'MfaController::verify');
$routes->post('mfa/verify', 'MfaController::verifyProcess');
$routes->get('mfa/cancel', 'MfaController::cancel');

// Shown by AuthFilter when a logged-in user opens a page their role cannot access
$routes->get('unauthorized', static function () {
    return service('response')
        ->setStatusCode(403)
        ->setBody('You do not have access to this page.');
});


/*
|--------------------------------------------------------------------------
| PUBLIC / CLIENT ROUTES
|--------------------------------------------------------------------------
*/

$routes->get('/', 'EportalController::index');

$routes->get('service-record', 'EportalController::service_record');
$routes->post('service-record/save', 'EportalController::save');
$routes->post('service-record/manualsave', 'EportalController::manualsave');

// NOTE: FeedbackController::csm() does not exist yet (it never existed in EportalController either)
$routes->get('csm/(:any)', 'FeedbackController::csm/$1');
$routes->get('csmtagalog/(:any)', 'FeedbackController::csmTagalog/$1');

$routes->post('submit-csm', 'FeedbackController::submitCSM');

$routes->get('feedback/hr/(:segment)', 'FeedbackController::form/$1');
$routes->post('feedback/save', 'FeedbackController::save');
$routes->get('feedback/print/hr/(:segment)', 'FeedbackController::printHr/$1');

$routes->get('feedback/print/english/(:num)', 'FeedbackController::printEnglish/$1');
$routes->get('feedback/print/tagalog/(:num)', 'FeedbackController::printTagalog/$1');


/*
|--------------------------------------------------------------------------
| PUBLIC: TRAINING IMPACT ASSESSMENT FORMS (opened from emailed links)
|--------------------------------------------------------------------------
*/

// Annex D (self-rater)
$routes->get('tia_ad', 'HrController::create');
$routes->post('tia_store', 'HrController::store');
$routes->get('tia_submitted/(:segment)', 'HrController::submitted/$1');

// Annex E (superior rater)
$routes->get('tia_ae', 'TiaSuperiorController::create');
$routes->post('tia_superior_store', 'TiaSuperiorController::store');


/*
|--------------------------------------------------------------------------
| ANY LOGGED-IN ROLE (IT, HR and SUPERIOR)
| SUPERIOR can only reach these pages
|--------------------------------------------------------------------------
*/

$routes->group('', ['filter' => 'auth'], function ($routes) {

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
    $routes->post('tia/notify-superior', 'HrController::notifySuperior');
    $routes->get('tia/trainings-template', 'HrController::trainingsTemplate');
    $routes->get('tia/export-trainings', 'HrController::exportTrainings');
    $routes->post('tia/import-trainings', 'HrController::importTrainings');

    $routes->post('tia/send-superior-emails', 'TiaSuperiorController::sendEmails');
    $routes->get('tia-superior/rate/(:segment)/(:num)', 'TiaSuperiorController::rate/$1/$2');

    $routes->get('tia-superior', 'TiaSuperiorController::index');
    $routes->get('tia-superior/participants/(:segment)', 'TiaSuperiorController::getParticipants/$1');
    $routes->get('tia-superior/monitoring', 'TiaSuperiorController::monitoring');

    $routes->get('tia-report', 'TiaReportController::index');
    $routes->get('tia-report-superior', 'TiaReportController::superior');
    $routes->get('tia-report-annex-h', 'TiaReportController::annexH');
    $routes->get('tia-report-monitoring', 'TiaReportController::monitoring');
    $routes->get('tia-report-monitoring/view/(:num)', 'TiaReportController::viewSuperior/$1');
    $routes->get('tia-report-participant-monitoring', 'TiaReportController::participantMonitoring');
    $routes->get('tia-report-participant-monitoring/view/(:num)', 'TiaReportController::viewParticipant/$1');
    $routes->get('tia-report-annex-g', 'TiaReportController::annexG');
    $routes->get('tia-report-annex-j', 'TiaReportController::annexJ');
    $routes->post('tia-report-annex-j/save', 'TiaReportController::saveAnnexJ');
    $routes->get('tia-report/export', 'TiaReportController::exportD');
    $routes->get('tia-report-superior/export', 'TiaReportController::exportE');
    $routes->get('tia-report-bulk', 'TiaReportController::bulk');
    $routes->get('tia-report-bulk/export', 'TiaReportController::bulkExport');

    // Logout
    $routes->get('logout', 'EportalController::logout');
});


/*
|--------------------------------------------------------------------------
| IT + HR ADMINISTRATORS ONLY
|--------------------------------------------------------------------------
*/

$routes->group('', ['filter' => 'auth:admin'], function ($routes) {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('manual', 'EportalController::manual');


    /*
    |--------------------------------------------------------------------------
    | REQUESTS
    |--------------------------------------------------------------------------
    */

    $routes->get('request', 'RequestController::index');
    $routes->post('request/save', 'RequestController::save');
    $routes->post('request/start-processing', 'RequestController::startProcessing');
    $routes->post('request/update/(:num)', 'RequestController::update/$1');
    $routes->post('request/cancel/(:num)', 'RequestController::cancel/$1');
    $routes->get('request/history/(:num)', 'RequestController::history/$1');


    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    $routes->get('reports', 'ReportController::index');

    $routes->get('reports/print', 'ExportController::printHrRequestReport');

    $routes->get('exports/satisfaction-excel/(:any)', 'ExportController::exportSatisfactionExcel/$1');
    $routes->get('exports/satisfaction-excel', 'ExportController::exportSatisfactionExcel');
    $routes->get('client-satisfaction-report', 'ExportController::report');

    $routes->get('export/hr-request', 'ExportController::exportHrRequestReport');
    $routes->get('export/reports-pdf', 'ExportController::exportReportsPdf');
    $routes->get('export/reports-csv', 'ExportController::exportReportsCsv');


    /*
    |--------------------------------------------------------------------------
    | FEEDBACK
    |--------------------------------------------------------------------------
    */

    $routes->get('feedback', 'FeedbackController::index');


    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */

    $routes->get('users', 'UserController::index');
    $routes->post('users/store', 'UserController::store');
    $routes->post('users/update/(:num)', 'UserController::update/$1');
    $routes->post('users/deactivate/(:num)', 'UserController::deactivate/$1');
    $routes->post('users/activate/(:num)', 'UserController::activate/$1');
    $routes->post('users/reset-mfa/(:num)', 'UserController::resetMfa/$1');


    /*
    |--------------------------------------------------------------------------
    | ACTIVITY LOGS  (IT Administrator only - checked in the controller)
    |--------------------------------------------------------------------------
    */

    $routes->get('activity-logs', 'ActivityLogController::index');
    $routes->get('activity-logs/export', 'ActivityLogController::export');


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEES
    |--------------------------------------------------------------------------
    */

    $routes->get('employees', 'EmployeeController::index');
    $routes->post('employees/store', 'EmployeeController::store');
    $routes->post('employees/update/(:num)', 'EmployeeController::update/$1');
    $routes->post('employees/delete/(:num)', 'EmployeeController::delete/$1');
    $routes->get('employees/export', 'EmployeeController::export');
    $routes->get('employees/template', 'EmployeeController::template');
    $routes->post('employees/import', 'EmployeeController::import');
    $routes->get('employees/all', 'EmployeeController::all');
    $routes->get('employees/permanent', 'EmployeeController::permanent');
    $routes->get('employees/non-permanent', 'EmployeeController::nonPermanent');


    /*
    |--------------------------------------------------------------------------
    | SUPERIORS
    |--------------------------------------------------------------------------
    */

    $routes->get('superiors', 'SuperiorController::index');
    $routes->post('superiors/store', 'SuperiorController::store');
    $routes->post('superiors/update/(:num)', 'SuperiorController::update/$1');
    $routes->post('superiors/delete/(:num)', 'SuperiorController::delete/$1');
    $routes->post('superiors/quick-add', 'SuperiorController::quickAdd');
    $routes->get('superiors/all', 'SuperiorController::all');
});