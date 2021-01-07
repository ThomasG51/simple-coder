<?php

use App\Controller\ErrorsController;
use Lib\Exceptions\BadGatewayException;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\ForbiddenException;
use Lib\Exceptions\NotAuthorizedException;
use Lib\Exceptions\NotFoundException;
use Lib\Exceptions\TokenNotValidException;
use Lib\Router\Router;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


// Autoloader
require '../vendor/autoload.php';

// Environment
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env', __DIR__.'/../.env.local');

$request = Request::createFromGlobals();

$session = new Session();
$session->start();

$error = new ErrorsController($request, $session);

try
{
    $router = new Router();
    $route = $router->getRouteFromRequest($request);
    $response = $route->run($request, $session);
    $response->send();
}
catch(NotFoundException $e)
{
    $notFound = $error->renderException($e->getCode(), $e->getMessage())->send();
}
catch(BadGatewayException $e)
{
    $badGateway = $error->renderException($e->getCode(), $e->getMessage())->send();
}
catch(BadRequestException $e)
{
    $badRequest = $error->renderException($e->getCode(), $e->getMessage())->send();
}
catch(ForbiddenException $e)
{
    $forbidden = $error->renderException($e->getCode(), $e->getMessage())->send();
}
catch(NotAuthorizedException $e)
{
    $notAuthorized = $error->renderException($e->getCode(), $e->getMessage())->send();
}
catch(TokenNotValidException $e)
{
    $tokenNotValid = $error->renderException($e->getCode(), $e->getMessage())->send();
}