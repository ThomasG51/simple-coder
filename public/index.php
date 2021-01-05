<?php

use Lib\Exceptions\BadGatewayException;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\ForbiddenException;
use Lib\Exceptions\NotAuthorizedException;
use Lib\Exceptions\NotFoundException;
use Lib\Exceptions\TokenNotValidException;
use Lib\Router\Router;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;


// Autoloader
require '../vendor/autoload.php';

// Environment
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env', __DIR__.'/../.env.local');

try
{
    $request = Request::createFromGlobals();
    $router = new Router();
    $route = $router->getRouteFromRequest($request);
    $response = $route->run($request);
    $response->send();
}
catch(NotFoundException $e)
{
    echo $e->getCode() . ' ' . $e->getMessage();
}
catch(BadGatewayException $e)
{
    echo $e->getCode() . ' ' . $e->getMessage();
}
catch(BadRequestException $e)
{
    echo $e->getCode() . ' ' . $e->getMessage();
}
catch(ForbiddenException $e)
{
    echo $e->getCode() . ' ' . $e->getMessage();
}
catch(NotAuthorizedException $e)
{
    echo $e->getCode() . ' ' . $e->getMessage();
}
catch(TokenNotValidException $e)
{
    echo $e->getCode() . ' ' . $e->getMessage();
}