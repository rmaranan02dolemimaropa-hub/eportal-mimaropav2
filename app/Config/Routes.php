<?php

use App\Controllers\EportalController;
use CodeIgniter\Router\RouteCollection;


/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('/', 'EportalController::index');
$routes->get('service-record', 'EportalController::service_record');
$routes->post('service-record/save', 'EportalController::save');
