<?php

namespace Lib\Router;

use Lib\Exceptions\RouteNotFoundException;
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
    
    public function __construct()
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
    }

    /**
     * @param Request $request
     * @return Route|null
     */
    public function getRouteFromRequest(Request $request) : ?Route
    {
        foreach ($this->routesCollection as $route)
        {
            if($route->checkMatch($request))
            {
                return $route;
            }
        }
    }
}