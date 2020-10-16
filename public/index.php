<?php

use Lib\Router\Router;
use Symfony\Component\HttpFoundation\Request;

require '../vendor/autoload.php';

$request = Request::createFromGlobals();

$router = new Router;
dump($router->callController($request));