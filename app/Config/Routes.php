<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'MainController::home');

//PENDAFTARAN
$routes->get('/pendaftaran/add', 'PendaftaranController::addPendaftaran');
$routes->post('/pendaftaran/add', 'PendaftaranController::actionAddPendaftaran');
$routes->get('/pendaftaran/list', 'PendaftaranController::listPendaftaran');
$routes->get('/pendaftaran/list/(:any)/(:any)', 'PendaftaranController::listPendaftaran/$1/$2');
$routes->get('/pendaftaran/del/(:any)', 'PendaftaranController::delPendaftaran/$1');
$routes->get('/pendaftaran/getpasien/(:any)', 'PendaftaranController::getPasien/$1');

//PELAYANAN
$routes->get('/pelayanan/add', 'PelayananController::addPelayanan');
$routes->post('/pelayanan/add', 'PelayananController::actionAddPelayanan');
$routes->get('/pelayanan/getpendaftaran/(:any)/(:any)', 'PelayananController::getPendaftaran/$1/$2');
$routes->get('/movekun', 'PelayananController::moveFromKunjungan');
$routes->get('/gantiid', 'PelayananController::gantiIdLama');
$routes->get('/pelayanan/list', 'PelayananController::listPelayanan');
$routes->get('/pelayanan/list/(:any)', 'PelayananController::listPelayanan/$1');
$routes->get('/pelayanan/del/(:any)', 'PelayananController::delPelayanan/$1');
$routes->get('/pelayanan/edit/(:any)', 'PelayananController::editPelayanan/$1');
$routes->post('/pelayanan/edit/(:any)', 'PelayananController::actionEditPelayanan/$1');

//Api BPJS
$routes->get('/bpjs/getpoli', 'ApiBpjsController::getPoli');
$routes->get('/bpjs/getprovider', 'ApiBpjsController::getProvider');
$routes->get('/bpjs/getdokter', 'ApiBpjsController::getDokter');
$routes->get('/bpjs/getkesadaran', 'ApiBpjsController::getKesadaran');
$routes->get('/bpjs/getprognosa', 'ApiBpjsController::getPrognosa');
$routes->get('/bpjs/getpeserta/(:any)/(:any)', 'ApiBpjsController::getPeserta/$1/$2');
$routes->get('/bpjs/getpendaftaran/(:any)', 'ApiBpjsController::getPendaftaran/$1');
$routes->get('/bpjs/delpendaftaran/(:any)/(:any)/(:any)/(:any)', 'ApiBpjsController::delPendaftaran/$1/$2/$3/$4');
$routes->get('/bpjs/getriwayatkunjungan/(:any)', 'ApiBpjsController::getRiwayatKunjungan/$1');
$routes->get('/bpjs/delriwayatkunjungan/(:any)', 'ApiBpjsController::delRiwayatKunjungan/$1');

$routes->post('/bpjs/addtindakan', 'ApiBpjsController::addTindakan');
$routes->get('/bpjs/gettindakan/(:any)', 'ApiBpjsController::getTindakan/$1');
$routes->get('/bpjs/getdiagnosa/(:any)', 'ApiBpjsController::getDiagnosa/$1');
$routes->get('/bpjs/getalergi/(:any)', 'ApiBpjsController::getAlergi/$1');
$routes->get('/bpjs/getstatuspulang/(:any)', 'ApiBpjsController::getStatusPulang/$1');
$routes->get('/bpjs/getrefspesialis', 'ApiBpjsController::getRefSpesialis');
$routes->get('/bpjs/getrefsubspesialis/(:any)', 'ApiBpjsController::getRefSubSpesialis/$1');
$routes->get('/bpjs/getrefsarana', 'ApiBpjsController::getRefSarana');
$routes->get('/bpjs/getrefkhusus', 'ApiBpjsController::getRefKhusus');
$routes->get('/bpjs/getfasketrujukansubspesialis/(:any)/(:any)/(:any)', 'ApiBpjsController::getFasketRujukanSubSpesialis/$1/$2/$3');
$routes->get('/bpjs/getfasketrujukankhusus/(:any)/(:any)/(:any)/(:any)', 'ApiBpjsController::getFasketRujukanKhusus/$1/$2/$3/$4');
$routes->get('/bpjs/getreftindakan/(:any)', 'ApiBpjsController::getRefTindakan/$1');
$routes->get('/gantitgl', 'ApiBpjsController::gantiTgl');
$routes->get('/gantitglkun', 'ApiBpjsController::gantiTglPraktekKunjungan');
$routes->get('/benerinrm', 'ApiBpjsController::benerinRm');

//Pasien
$routes->get('/pasien', 'PasienController::allPasien');
$routes->get('/pasien/(:any)', 'PasienController::pasien/$1');

//komponen
$routes->get('/com/tindakan/(:any)', 'ApiBpjsController::getTindakanKomponen/$1');
$routes->get('/com/tindakan/(:any)/(:any)', 'ApiBpjsController::getTindakanKomponen/$1/$2');
$routes->get('/com/dokter', 'ApiBpjsController::getDokterKomponen');
$routes->get('/com/dokter/(:any)', 'ApiBpjsController::getDokterKomponen/$1');
$routes->get('/com/sadar', 'ApiBpjsController::getKesadaranKomponen');
$routes->get('/com/sadar/(:any)', 'ApiBpjsController::getKesadaranKomponen/$1');
$routes->get('/com/poli', 'ApiBpjsController::getPoliKomponen');
$routes->get('/com/poli/(:any)', 'ApiBpjsController::getPoliKomponen/$1');
$routes->get('/com/statuspulang/(:any)', 'ApiBpjsController::getStatusPulangKomponen/$1');
$routes->get('/com/statuspulang/(:any)/(:any)', 'ApiBpjsController::getStatusPulangKomponen/$1/$2');
$routes->get('/com/alergi/(:any)', 'ApiBpjsController::getAlergiKomponen/$1');
$routes->get('/com/alergi/(:any)/(:any)', 'ApiBpjsController::getAlergiKomponen/$1/$2');
$routes->get('/com/prognosa', 'ApiBpjsController::getPrognosaKomponen');
$routes->get('/com/prognosa/(:any)', 'ApiBpjsController::getPrognosaKomponen/$1');
$routes->get('/com/khusus', 'ApiBpjsController::getRefKhususKomponen');
$routes->get('/com/khusus/(:any)', 'ApiBpjsController::getRefKhususKomponen/$1');
$routes->get('/com/sarana', 'ApiBpjsController::getRefSaranaKomponen');
$routes->get('/com/sarana/(:any)', 'ApiBpjsController::getRefSaranaKomponen/$1');
