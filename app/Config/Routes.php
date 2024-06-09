<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//PENDAFTARAN
$routes->get('/pendaftaran/add', 'PendaftaranController::addPendaftaran');
$routes->post('/pendaftaran/add', 'PendaftaranController::actionAddPendaftaran');

//Api BPJS
$routes->get('/bpjs/getpeserta/(:any)/(:any)', 'ApiBpjsController::getPeserta/$1/$2');
