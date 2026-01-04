<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default Controller
$routes->get('/', 'Home::index');

// 404 Override
$routes->set404Override('Home::error_404');

// Custom Routes from CI3
$routes->get('integration/connect/facebook', 'Social_accounts::index');
$routes->get('anaytics/messenger/(:num)', 'Messenger_bot_analytics::result/$1');

// Login , Signup routes - match both GET and POST
$routes->match(['get', 'post'], 'home/login', 'Home::login');
$routes->match(['get', 'post'], 'home/login/(:num)', 'Home::login/$1');
$routes->match(['get', 'post'], 'home/sign_up', 'Home::sign_up');

// Dashboard routes
$routes->match(['get', 'post'], 'dashboard', 'Dashboard::index');

