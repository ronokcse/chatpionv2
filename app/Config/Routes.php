<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default Controller

// 404 Override
$routes->set404Override('App\Controllers\Home::error_404');


