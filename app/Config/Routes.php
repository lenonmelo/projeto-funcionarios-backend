<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {

    // Rotas referente ao centros de custos
    $routes->get('centrosCusto', 'CentrosCustoController::index');
    $routes->get('centrosCusto/(:num)', 'CentrosCustoController::show/$1');
    $routes->post('centrosCusto', 'CentrosCustoController::create');
    $routes->put('centrosCusto/(:num)', 'CentrosCustoController::update/$1');
    $routes->delete('centrosCusto/(:num)', 'CentrosCustoController::delete/$1');
    $routes->get('centrosCusto/getAll', 'CentrosCustoController::getAll');

    // Rotas referente aos cargos
    $routes->get('cargos', 'CargosController::index');
    $routes->get('cargos/(:num)', 'CargosController::show/$1');
    $routes->post('cargos', 'CargosController::create');
    $routes->put('cargos/(:num)', 'CargosController::update/$1');
    $routes->delete('cargos/(:num)', 'CargosController::delete/$1');

    // Rotas referente aos departamentos
    $routes->get('departamentos', 'DepartamentosController::index');
    $routes->get('departamentos/(:num)', 'DepartamentosController::show/$1');
    $routes->post('departamentos', 'DepartamentosController::create');
    $routes->put('departamentos/(:num)', 'DepartamentosController::update/$1');
    $routes->delete('departamentos/(:num)', 'DepartamentosController::delete/$1');
    $routes->get('departamentos/searchDepartamentosByCentroCusto/(:num)', 'DepartamentosController::searchDepartamentosByCentroCusto/$1');
    $routes->get('departamentos/getAll', 'DepartamentosController::getAll');

    // Rotas referente aos usuarios
    $routes->get('usuarios', 'UsuariosController::index');
    $routes->get('usuarios/(:num)', 'UsuariosController::show/$1');
    $routes->post('usuarios', 'UsuariosController::create');
    $routes->put('usuarios/(:num)', 'UsuariosController::update/$1');
    $routes->delete('usuarios/(:num)', 'UsuariosController::delete/$1');
    $routes->get('usuarios/searchUsuariosByDepartamentos/(:num)', 'UsuariosController::searchUsuariosByDepartamentos/$1');
    $routes->post('usuarios/importar', 'UsuariosController::import');

    //Rotas de auenticações
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/valida', 'AuthController::validarToken');
});
