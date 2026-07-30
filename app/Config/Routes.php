<?php

use App\Controllers\EportalController;
use CodeIgniter\Router\RouteCollection;


/** @var RouteCollection $routes */
// $routes->get('/', 'Home::index');

// $routes->get('eportal', 'EportalController::index');
// $routes->get('service-record', 'EportalController::service_record');
// $routes->post('service-record/save', 'EportalController::save');
// $routes->get('dashboard', 'EportalController::dashboard');
// $routes->get('users', 'EportalController::users');

// $routes->get('login', 'EportalController::login');
// $routes->post('login/login_process', 'EportalController::login_process');
// $routes->get('logout', 'EportalController::logout');
// $routes->get('request','EportalController::request');

// $routes->group('it', ['filter' => 'auth:IT ADMINISTRATOR'], function($routes){

//     $routes->get('dashboard', 'ITController::dashboard');

// });

// $routes->group('hr', ['filter' => 'auth:HR ADMINISTRATOR'], function($routes){

//     $routes->get('dashboard', 'HRController::dashboard');

// });

// $routes->get('/unauthorized', function(){

//     return "403 Unauthorized";

// });

// /*
// |--------------------------------------------------------------------------
// | USER MANAGEMENT
// |--------------------------------------------------------------------------
// */

// $routes->get('/users', 'EportalController::users');

// $routes->post('/users/store', 'EportalController::storeUser');

// $routes->post('/users/update/(:num)', 'EportalController::updateUser/$1');

// $routes->post('/users/delete/(:num)', 'EportalController::deleteUser/$1');

// $routes->get('export/hr-request', 'ExportController::exportHrRequestReport');

// $routes->post('request/update/(:num)', 'EportalController::updateRequest/$1');

// $routes->post('request/delete/(:num)', 'EportalController::deleteRequest/$1');

// $routes->post('request/save', 'EportalController::saveRequest');

$routes->get('login', 'EportalController::login');
$routes->post('login/login_process', 'EportalController::login_process');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

$routes->get('eportal', 'EportalController::index');

$routes->get('service-record', 'EportalController::service_record');
$routes->post('service-record/save', 'EportalController::save');
$routes->get('export/hr-request', 'ExportController::exportHrRequestReport');
$routes->get('reports', 'EportalController::reports');
$routes->get('reports/print', 'ExportController::printHrRequestReport');
$routes->get('export/reports-pdf', 'ExportController::exportReportsPdf');
$routes->get('export/reports-csv', 'ExportController::exportReportsCsv');
$routes->get('csm/(:any)', 'EportalController::csm/$1');

$routes->post('submit-csm', 'EportalController::submitCSM');



$routes->group('', ['filter' => 'auth'], function ($routes) {




    $routes->get('dashboard', 'EportalController::dashboard');
    $routes->get('request', 'EportalController::request');
    $routes->get('logout', 'EportalController::logout');



    $routes->post('request/save', 'EportalController::saveRequest');
    $routes->post('request/update/(:num)', 'EportalController::updateRequest/$1');
    $routes->post('request/delete/(:num)', 'EportalController::deleteRequest/$1');
    $routes->get('/users', 'EportalController::users');
    $routes->post('/users/store', 'EportalController::storeUser');
    $routes->post('/users/update/(:num)', 'EportalController::updateUser/$1');
    $routes->post('/users/delete/(:num)', 'EportalController::deleteUser/$1');
});

