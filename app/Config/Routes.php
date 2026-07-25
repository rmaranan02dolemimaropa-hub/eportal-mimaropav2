<?php

use App\Controllers\EportalController;
use CodeIgniter\Router\RouteCollection;


/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('eportal', 'EportalController::index');
$routes->get('service-record', 'EportalController::service_record');
$routes->post('service-record/save', 'EportalController::save');
$routes->get('dashboard', 'EportalController::dashboard');
$routes->get('users', 'EportalController::users');

$routes->get('login', 'EportalController::login');
$routes->post('login/login_process', 'EportalController::login_process');
$routes->get('login/logout', 'EportalController::logout');

$routes->group('it', ['filter' => 'auth:IT ADMINISTRATOR'], function($routes){

    $routes->get('dashboard', 'ITController::dashboard');

});

$routes->group('hr', ['filter' => 'auth:HR ADMINISTRATOR'], function($routes){

    $routes->get('dashboard', 'HRController::dashboard');

});

$routes->get('/unauthorized', function(){

    return "403 Unauthorized";

});

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT
|--------------------------------------------------------------------------
*/

$routes->get('/users', 'EportalController::users');

$routes->post('/users/store', 'EportalController::storeUser');

$routes->post('/users/update/(:num)', 'EportalController::updateUser/$1');

$routes->post('/users/delete/(:num)', 'EportalController::deleteUser/$1');



