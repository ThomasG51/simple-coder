<?php

namespace Lib\Router;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;

class Router 
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var array
     */
    private $routesCollection;
    
    public function __construct(Request $request)
    {
        // Get route collection from Yaml file
        $routesCollection = [];
        $routes = Yaml::parseFile('../config/routes.yaml');

        foreach($routes as $routesNames => $oneRoute)
        {
            $route = new Route($routesNames, $oneRoute['path'], $oneRoute['controller']);
            $routesCollection[$routesNames] = $route;
        }

        $this->routesCollection = $routesCollection;

        // Set request attribute
        $this->request = $request;

        // Start router
        $this->run();
    }
    
    /**
     * @param Request $request
     * @return Route
     */
    public function getRouteFromRequest(): Route
    {
        foreach ($this->routesCollection as $route)
        {
            if($route->checkMatch($this->request->getPathInfo()))
            {
                return $route;
            }
        }
    }

    public function run()
    {
        $route = $this->getRouteFromRequest();

        list($controller, $method) = explode("::", $route->getController());
        $args = $route->getArgs(); 

        // Comment fonctionne en detail le tableau en premier parametre pour instancier l'objet ?
        call_user_func_array([$controller, $method], $args);

        // $reflectionClass = new ReflectionClass($controller); a voir !!!

        dump($route);
    }
}