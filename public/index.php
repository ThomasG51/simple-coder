<?php

use Lib\Router\Router;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;


// Autoloader
require '../vendor/autoload.php';

// Environment
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env', __DIR__.'/../.env.local');

// Router
$request = Request::createFromGlobals();
$router = new Router();
$route = $router->getRouteFromRequest($request);
$response = $route->run($request);
$response->send();