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
// $routes->get('lista-rutas', 'Service\Busqueda::listaRutas');
$routes->get('lista-rutas/(:num)', 'Service\Busqueda::listaRutas/$1');

$routes->post('actualizar-rq', 'Service\Busqueda::actualizar_rq');
$routes->post('insertar-ruta', 'Service\Busqueda::insertarRuta');

$routes->get('lista-placas', 'Service\Busqueda::listaRutasPlacas');

// PARA MAPA Y LISTA ----------------------------------------------------------------
$routes->get('lista-rutas-detalle/(:num)', 'Service\Busqueda::listaRutasDetalle/$1');
//-------------------------------------------------------------------------------------


// $routes->get('lista-placas-direcciones', 'Service\Busqueda::listaPlacasConDirecciones');
$routes->get('lista-placas-direcciones', 'Service\Busqueda::listaPlacasConDirecciones/$1');



$routes->get('lista-placas-con-direcciones', 'Service\Busqueda::getListaPlacasConDirecciones');
$routes->get('detalle-vehiculo-direccion/(:segment)', 'Service\Busqueda::detalleVehiculoDireccion/$1');

//API_MTC
$routes->get('/', 'Service\ConsultingData::index');

$routes->get('rutas-usuario/(:num)/(:num)/(:segment)', 'Service\Busqueda::getRutasPorUsuario/$1/$2/$3');







