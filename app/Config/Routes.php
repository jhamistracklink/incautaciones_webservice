<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->presenter('service/usuarios');

// (Para consultar, Interno)
$routes->post('login', 'Service\Usuarios::signin');
$routes->get('busqueda/por-placa/(:segment)', 'Service\Busqueda::buscar_por_placa/$1');
$routes->post('busqueda/actualizar', 'Service\Busqueda::update_rq_ubi');
$routes->get('lista-rutas', 'Service\Busqueda::listaRutas');

$routes->post('actualizar-rq', 'Service\Busqueda::actualizar_rq');
$routes->post('insertar-ruta', 'Service\Busqueda::insertarRuta');

$routes->get('lista-placas', 'Service\Busqueda::listaRutasPlacas');

//API_MTC
$routes->get('/', 'Service\ConsultingData::index');


