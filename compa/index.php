<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/legal/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sessions
 */
session_start();


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes

$router->add('', ['controller' => 'Login', 'action' => 'index']);
$router->add('login/index', ['controller' => 'Login', 'action' => 'index']);
$router->add('roles/index', ['controller' => 'Roles', 'action' => 'index']);
$router->add('upload/index', ['controller' => 'Upload', 'action' => 'index']);
$router->add('dashboard/index', ['controller' => 'Dashboard', 'action' => 'index']);
$router->add('posts/index', ['controller' => 'Posts', 'action' => 'index']);
//$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('{controller}/{id:\d+}/{idp:\d+}/{action}');
/* $router->add('{controller}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']); */
    
$router->dispatch($_SERVER['QUERY_STRING']);
